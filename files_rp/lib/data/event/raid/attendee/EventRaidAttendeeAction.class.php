<?php

namespace rp\data\event\raid\attendee;

use rp\system\cache\runtime\EventRaidAttendeeRuntimeCache;
use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IPopoverAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\user\notification\UserNotificationHandler;
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
 * Executes event raid attendee related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event\Raid\Attendee
 * 
 * @method      EventRaidAttendeeEditor[]   getObjects()
 * @method      EventRaidAttendeeEditor     getSingleObject()
 */
class EventRaidAttendeeAction extends AbstractDatabaseObjectAction implements IPopoverAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = [
        'getPopover',
    ];

    protected ?EventRaidAttendee $attendee = null;

    /**
     * @inheritDoc
     */
    protected $className = EventRaidAttendeeEditor::class;

    /**
     * @inheritDoc
     */
    public function create(): EventRaidAttendee
    {
        $this->parameters['data']['created'] = TIME_NOW;

        return parent::create();
    }

    /**
     * @inheritDoc
     */
    public function delete(): array
    {
        // delete attendees
        parent::delete();

        foreach ($this->objects as $attendee) {
            if ($attendee->getCharacter()?->userID) {
                UserNotificationHandler::getInstance()->markAsConfirmed(
                    'status',
                    'info.daries.rp.raid.event.notification',
                    [$attendee->getCharacter()->userID],
                    [$attendee->getEvent()->eventID]
                );
            }
        }

        $this->unmarkItems();

        return [
            'objectIDs' => $this->objectIDs
        ];
    }

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $attendeeID = \reset($this->objectIDs);

        if ($attendeeID) {
            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->setObjectIDs([$attendeeID]);
            $attendeeList->readObjects();
            $attendee = $attendeeList->getSingleObject();

            if ($attendee) {
                WCF::getTPL()->assign('attendee', $attendee);
            } else {
                WCF::getTPL()->assign('unknownAttendee', true);
            }
        } else {
            WCF::getTPL()->assign('unknownAttendee', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('raidAttendeePreview', 'rp'),
        ];
    }

    /**
     * Returns the template for the attendee update status.
     */
    public function loadUpdateStatus(): array
    {
        $statusData = [];

        if ($this->attendee->getEvent()->getController()->isLeader()) {
            $statusData[EventRaidAttendee::STATUS_CONFIRMED] = WCF::getLanguage()->get('rp.event.raid.container.confirmed');
        }

        $statusData[EventRaidAttendee::STATUS_LOGIN] = WCF::getLanguage()->get('rp.event.raid.container.login');
        $statusData[EventRaidAttendee::STATUS_RESERVE] = WCF::getLanguage()->get('rp.event.raid.container.reserve');
        $statusData[EventRaidAttendee::STATUS_LOGOUT] = WCF::getLanguage()->get('rp.event.raid.container.logout');

        return [
            'template' => WCF::getTPL()->fetch('eventRaidAttendeeStatusDialog', 'rp', [
                'statusData' => $statusData,
            ])
        ];
    }

    /**
     * Unmarks attendees.
     */
    protected function unmarkItems(array $attendeeIDs = [])
    {
        if (empty($attendeeIDs)) {
            foreach ($this->getObjects() as $attendee) {
                $attendeeIDs[] = $attendee->attendeeID;
            }
        }

        if (!empty($attendeeIDs)) {
            ClipboardHandler::getInstance()->unmark($attendeeIDs, ClipboardHandler::getInstance()->getObjectTypeID('info.daries.rp.raid.attendee'));
        }
    }

    public function updateStatus(): array
    {
        foreach ($this->objects as $editor) {
            $update = [
                'status' => $this->parameters['status'],
            ];

            switch ($editor->getEvent()->distributionMode) {
                case 'role':
                    if ($this->parameters['distributionID']) {
                        $update['roleID'] = $this->parameters['distributionID'];
                    }
                    break;
            }

            $editor->update($update);

            if ($editor->getCharacter()?->userID) {
                UserNotificationHandler::getInstance()->fireEvent(
                    'status',
                    'info.daries.rp.raid.event.notification',
                    new EventRaidUserNotificationObject($editor->getEvent()),
                    [$editor->getCharacter()->userID],
                    ['objectID' => $editor->getEvent()->eventID]
                );
            }
        }

        $this->unmarkItems();

        return [
            'status' => $this->parameters['status'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function validateDelete(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        /** @var EventRaidAttendeeEditor $attendee */
        foreach ($this->objects as $attendee) {
            if (!$attendee->getCharacter() || $attendee->getCharacter()->userID !== WCF::getUser()->userID) {
                if (!$attendee->getEvent()->canDelete()) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        WCF::getSession()->checkPermissions(['user.rp.canViewCharacterProfile']);

        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }

    /**
     * Validates the `loadUpdateStatus` action.
     * 
     * @throws  UserInputException
     */
    public function validateLoadUpdateStatus(): void
    {
        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }

        $attendeeID = \reset($this->objectIDs);
        $this->attendee = EventRaidAttendeeRuntimeCache::getInstance()->getObject($attendeeID);
        if ($this->attendee === null) {
            throw new UserInputException('objectIDs');
        }

        if (!$this->attendee->getCharacter() ||
            $this->attendee->getCharacter()->userID != WCF::getUser()->userID ||
            $this->attendee->getEvent()->isClosed ||
            $this->attendee->getEvent()->getController()->isExpired()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates the `updateStatus` action.
     * 
     * @throws  UserInputException
     */
    public function validateUpdateStatus(): void
    {
        $this->readInteger('distributionID', true);
        $this->readInteger('status', true);

        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        if (!\in_array(
                $this->parameters['status'],
                [
                    EventRaidAttendee::STATUS_CONFIRMED,
                    EventRaidAttendee::STATUS_LOGIN,
                    EventRaidAttendee::STATUS_RESERVE,
                    EventRaidAttendee::STATUS_LOGOUT,
                ]
            )) {
            throw new UserInputException('status');
        }

        /** @var EventRaidAttendeeEditor $attendee */
        foreach ($this->objects as $attendee) {
            if (!$attendee->getCharacter() || $attendee->getCharacter()->userID !== WCF::getUser()->userID) {
                if (!$attendee->getEvent()->canEdit()) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }
}
