<?php

namespace rp\system\user\notification\event;

use rp\data\event\LikeableEvent;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\user\UserProfile;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TReactionUserNotificationEvent;
use wcf\system\user\notification\event\TTestableLikeUserNotificationEvent;
use wcf\system\user\notification\event\TTestableUserNotificationEvent;
use wcf\system\user\notification\object\LikeUserNotificationObject;
use wcf\system\user\storage\UserStorageHandler;
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
 * User notification event for event likes.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      LikeUserNotificationObject  getUserNotificationObject()
 */
class EventLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableLikeUserNotificationEvent {
        TTestableLikeUserNotificationEvent::canBeTriggeredByGuests insteadof TTestableUserNotificationEvent;
    }
    use TTestableEventUserNotificationEvent;
    use TTestableUserNotificationEvent;
    use TReactionUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * @inheritDoc
     */
    public function checkAccess(): bool
    {
        if (!ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)->canRead()) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    protected static function createTestLikeObject(UserProfile $recipient, UserProfile $author): LikeableEvent
    {
        return new LikeableEvent(self::getTestEvent($author));
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): void
    {
        // not supported
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . $this->additionalData['objectID']);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()
                ->getObject($this->getUserNotificationObject()->objectID)
                ->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID);
        $authors = \array_values($this->getAuthors());
        $count = \count($authors);

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.message.stacked', [
                    'author' => $this->author,
                    'authors' => $authors,
                    'count' => $count,
                    'event' => $event,
                    'others' => $count - 1,
                    'reactions' => $this->getReactionsForAuthors(),
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.message', [
                'author' => $this->author,
                'event' => $event,
                'reactions' => $this->getReactionsForAuthors(),
                'userNotificationObject' => $this->getUserNotificationObject(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected static function getTestLikeableObjectTypeName(): string
    {
        return 'info.daries.rp.likeableEvent';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.title.stacked', [
                    'count' => $count,
                    'timesTriggered' => $this->notification->timesTriggered,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.event.like.notification.title');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(): bool
    {
        if (!MODULE_LIKE) {
            return false;
        }

        return parent::isVisible();
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->objectID);
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
