<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TReactionUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentLikeUserNotificationEvent;
use wcf\system\user\notification\object\LikeUserNotificationObject;

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
 * User notification event for event comment likes.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      LikeUserNotificationObject  getUserNotificationObject()
 */
class EventCommentLikeUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableCommentLikeUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    use TReactionUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * Returns the liked comment's id.
     */
    protected function getCommentID(): int
    {
        // this is the `wcfN_like.objectID` value
        return $this->getUserNotificationObject()->objectID;
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
        return \sha1($this->eventID . '-' . $this->getCommentID());
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#comment' . $this->getCommentID();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID']);
        $authors = \array_values($this->getAuthors());
        $count = \count($authors);

        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.message.stacked', [
                    'author' => $this->author,
                    'authors' => $authors,
                    'commentID' => $this->getCommentID(),
                    'count' => $count,
                    'event' => $event,
                    'others' => $count - 1,
                    'reactions' => $this->getReactionsForAuthors(),
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.message', [
                'author' => $this->author,
                'commentID' => $this->getCommentID(),
                'event' => $event,
                'reactions' => $this->getReactionsForAuthors(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.like.title.stacked', [
                    'count' => $count,
                    'timesTriggered' => $this->notification->timesTriggered,
            ]);
        }

        return $this->getLanguage()->get('rp.user.notification.eventComment.like.title');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->additionalData['objectID']);
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
