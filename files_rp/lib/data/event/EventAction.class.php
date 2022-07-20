<?php

namespace rp\data\event;

use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use rp\system\log\modification\EventModificationLogHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IPopoverAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\comment\CommentHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\moderation\queue\ModerationQueueActivationManager;
use wcf\system\reaction\ReactionHandler;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/*  Project:    Raidplaner: Core
 *  Package:    info.daries.rp
 *  Link:       http://daries.info
 *
 *  Copyright (C) 2018-2022 Daries.info Developer Team
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Executes event-related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 * 
 * @method      EventEditor[]   getObjects()
 * @method      EventEditor     getSingleObject()
 */
class EventAction extends AbstractDatabaseObjectAction implements IPopoverAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getPopover', 'markAllAsRead'];

    /**
     * event object
     */
    protected Event $event;

    public function changeEventAppointmentStatus(): array
    {
        $user = UserProfileRuntimeCache::getInstance()->getObject($this->parameters['userID']);
        $additionalData = $this->event->additionalData;
        $appointments = $additionalData['appointments'] ?? ['accepted' => [], 'canceled' => [], 'maybe' => []];

        if ($this->parameters['exists']) {
            foreach (['accepted', 'canceled', 'maybe'] as $status) {
                foreach ($appointments[$status] as $key => $userID) {
                    if ($userID === $user->userID) {
                        unset($appointments[$status][$key]);
                    }
                }
            }
        }

        $appointments[$this->parameters['status']][] = $user->userID;
        $additionalData['appointments'] = $appointments;

        $eventEditor = new EventAction([$this->event], 'update', ['data' => [
                'additionalData' => \serialize($additionalData)
        ]]);
        $eventEditor->executeAction();

        WCF::getTPL()->assign([
            'user' => $user
        ]);

        return [
            'status' => $this->parameters['status'],
            'template' => WCF::getTPL()->fetch('userListItem', 'rp'),
            'userID' => $user->userID
        ];
    }

    /**
     * cancel raid events.
     */
    public function cancel(): void
    {
        foreach ($this->getObjects() as $event) {
            if ($event->isCanceled) {
                continue;
            }

            $event->update([
                'isCanceled' => 1,
            ]);

            EventModificationLogHandler::getInstance()->cancel(
                $event->getDecoratedObject()
            );
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * @inheritDoc
     */
    public function create(): Event
    {
        if (!isset($this->parameters['data']['userID'])) $this->parameters['data']['userID'] = WCF::getUser()->userID;
        if (!isset($this->parameters['data']['username'])) $this->parameters['data']['username'] = WCF::getUser()->username;

        $this->parameters['data']['isDisabled'] = WCF::getSession()->getPermission('user.rp.canCreateEventWithoutModeration') ? 0 : 1;
        $this->parameters['data']['created'] = TIME_NOW;

        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        /** @var Event $event */
        $event = parent::create();
        $eventEditor = new EventEditor($event);

        // update search index
        SearchIndexManager::getInstance()->set(
            'info.daries.rp.event',
            $event->eventID,
            $event->notes,
            $event->getTitle(),
            $event->created,
            $event->userID,
            $event->username,
        );

        // save embedded objects
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                $eventEditor->update(['hasEmbeddedObjects' => 1]);
            }
        }

        if (!empty($updates)) {
            $eventEditor = new EventEditor($event);
            $eventEditor->update($updates);
        }

        if (!$event->isDisabled) {
            $action = new EventAction([$eventEditor], 'triggerPublication');
            $action->executeAction();
        }

        // mark event for moderated content
        if ($event->isDisabled) {
            ModerationQueueActivationManager::getInstance()->addModeratedContent('info.daries.rp.event', $event->eventID);
        }

        return new Event($event->eventID);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $eventIDs = [];
        foreach ($this->getObjects() as $event) {
            $eventIDs[] = $event->eventID;

            EventModificationLogHandler::getInstance()->delete(
                $event->getDecoratedObject()
            );
        }

        parent::delete();

        if (!empty($eventIDs)) {
            // delete like data
            ReactionHandler::getInstance()->removeReactions('info.daries.rp.likeableEvent', $eventIDs);

            // delete comments
            CommentHandler::getInstance()->deleteObjects('info.daries.rp.eventComment', $eventIDs);

            // delete embedded object references
            MessageEmbeddedObjectManager::getInstance()->removeObjects('info.daries.rp.event.notes', $eventIDs);

            // delete event from search index
            SearchIndexManager::getInstance()->delete('info.daries.rp.event', $eventIDs);

            // delete modification log entries except for deleting the events
            EventModificationLogHandler::getInstance()->deleteLogs($eventIDs, ['delete']);
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Disables events.
     */
    public function disable()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $event) {
            $event->update([
                'isDisabled' => 1
            ]);

            EventModificationLogHandler::getInstance()->disable(
                $event->getDecoratedObject()
            );

            // add moderated content
            ModerationQueueActivationManager::getInstance()->addModeratedContent('info.daries.rp.event', $event->eventID);
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Enables events.
     */
    public function enable(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $eventIDs = [];
        foreach ($this->getObjects() as $event) {
            $eventIDs[] = $event->eventID;
            $event->update([
                'isDisabled' => 0
            ]);

            EventModificationLogHandler::getInstance()->enable(
                $event->getDecoratedObject()
            );
        }

        // trigger publication
        if (!empty($eventIDs)) {
            $action = new EventAction($eventIDs, 'triggerPublication');
            $action->executeAction();

            $this->removeModeratedContent($eventIDs);
        }
    }

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $eventID = \reset($this->objectIDs);

        if ($eventID) {
            $event = ViewableEventRuntimeCache::getInstance()->getObject($eventID);
            if ($event) {
                WCF::getTPL()->assign('event', $event);
            } else {
                WCF::getTPL()->assign('unknownEvent', true);
            }
        } else {
            WCF::getTPL()->assign('unknownEvent', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('eventPreview', 'rp'),
        ];
    }

    /**
     * Marks all articles as read.
     */
    public function markAllAsRead(): void
    {
        VisitTracker::getInstance()->trackTypeVisit('info.daries.rp.event');

        // reset storage
        if (WCF::getUser()->userID) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');
        }
    }

    /**
     * Marks events as read.
     */
    public function markAsRead(): void
    {
        if (empty($this->parameters['visitTime'])) {
            $this->parameters['visitTime'] = TIME_NOW;
        }

        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $event) {
            VisitTracker::getInstance()->trackObjectVisit(
                'info.daries.rp.event',
                $event->eventID,
                $this->parameters['visitTime']
            );
        }

        // reset storage
        if (WCF::getUser()->userID) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');
        }
    }

    /**
     * Removes moderated content events for given event ids.
     */
    protected function removeModeratedContent(array $eventIDs): void
    {
        ModerationQueueActivationManager::getInstance()->removeModeratedContent('info.daries.rp.event', $eventIDs);
    }

    /**
     * Restores events.
     */
    public function restore(): void
    {
        foreach ($this->getObjects() as $event) {
            if (!$event->isDeleted) {
                continue;
            }

            $event->update([
                'deleteTime' => 0,
                'isDeleted' => 0,
            ]);

            EventModificationLogHandler::getInstance()->restore(
                $event->getDecoratedObject()
            );
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Trashes events.
     */
    public function trash(): void
    {
        foreach ($this->getObjects() as $event) {
            if ($event->isDeleted) {
                continue;
            }

            $event->update([
                'deleteTime' => TIME_NOW,
                'isDeleted' => 1,
            ]);

            EventModificationLogHandler::getInstance()->trash(
                $event->getDecoratedObject(),
                $this->parameters['data']['reason']
            );
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Triggers the publication of events.
     */
    public function triggerPublication(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        parent::update();

        foreach ($this->getObjects() as $event) {
            // save embedded objects
            if (!empty($this->parameters['notes_htmlInputProcessor'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
                if ($event->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                    $event->update(['hasEmbeddedObjects' => $event->hasEmbeddedObjects ? 0 : 1]);
                }
            }

            // update search index
            SearchIndexManager::getInstance()->set(
                'info.daries.rp.event',
                $event->eventID,
                $this->parameters['data']['notes'] ?? $event->notes,
                $this->parameters['data']['title'] ?? $event->getTitle(),
                $this->parameters['data']['created'] ?? $event->created,
                $this->parameters['data']['userID'] ?? $event->userID,
                $this->parameters['data']['username'] ?? $event->username
            );

            EventModificationLogHandler::getInstance()->edit(
                $event->getDecoratedObject(),
                $this->parameters['reason'] ?? ''
            );
        }
    }

    public function validateChangeEventAppointmentStatus(): void
    {
        $this->readInteger('eventID');
        $this->readString('status');
        $this->readInteger('userID');
        $this->readBoolean('exists', true);

        $this->event = EventRuntimeCache::getInstance()->getObject($this->parameters['eventID']);

        if ($this->event->objectTypeID !== ObjectTypeCache::getInstance()->getObjectTypeIDByName('info.daries.rp.eventController', 'info.daries.rp.event.appointment')) {
            throw new PermissionDeniedException();
        }

        if (!$this->event->canRead()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates parameters to cancel events.
     */
    public function validateCancel(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canCancel()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Validates parameters to delete events.
     *
     * @throws  PermissionDeniedException
     * @throws  UserInputException
     */
    public function validateDelete(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canDelete()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Validates parameters to disable events.
     */
    public function validateDisable(): void
    {
        $this->validateEnable();
    }

    /**
     * Validates parameters to enable events.
     */
    public function validateEnable(): void
    {
        WCF::getSession()->checkPermissions(['mod.rp.canModerateEvent']);

        if (empty($this->objects)) {
            $this->readObjects();
            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if ($event->isDeleted) {
                throw new UserInputException('objectIDs');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        WCF::getSession()->checkPermissions(['user.rp.canReadEvent']);

        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }

    /**
     * Validates the mark all as read action.
     */
    public function validateMarkAllAsRead(): void
    {
        // does nothing
    }

    /**
     * Validates parameters to restore events.
     */
    public function validateRestore(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canRestore()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Validates parameters to trash events.
     */
    public function validateTrash(): void
    {
        $this->readString('reason', true, 'data');

        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canTrash()) {
                throw new PermissionDeniedException();
            }
        }
    }
}
