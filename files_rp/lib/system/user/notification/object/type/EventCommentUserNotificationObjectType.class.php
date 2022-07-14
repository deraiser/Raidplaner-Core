<?php

namespace rp\system\user\notification\object\type;

use rp\data\event\Event;
use wcf\data\comment\Comment;
use wcf\data\comment\CommentList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\user\notification\object\CommentUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;
use wcf\system\user\notification\object\type\ICommentUserNotificationObjectType;
use wcf\system\user\notification\object\type\IMultiRecipientCommentUserNotificationObjectType;
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
 * Represents a comment notification object type for comments on events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 */
class EventCommentUserNotificationObjectType extends AbstractUserNotificationObjectType implements
ICommentUserNotificationObjectType, IMultiRecipientCommentUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = CommentUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Comment::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = CommentList::class;

    /**
     * @inheritDoc
     */
    public function getOwnerID($objectID): int
    {
        $sql = "SELECT      event.userID
                FROM        wcf1_comment comment
                INNER JOIN  rp1_event event
                ON          event.eventID = comment.objectID
                WHERE       comment.commentID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$objectID]);

        return $statement->fetchSingleColumn() ?: 0;
    }

    /**
     * @inheritDoc
     */
    public function getRecipientIDs(Comment $comment): array
    {
        $event = new Event($comment->objectID);
        \assert($event->eventID !== 0);

        $leaders = [];
        if ($event->isRaidEvent()) {
            $leaders = $event->leaders;
        }
        $users = UserProfileRuntimeCache::getInstance()->getObjects($leaders);

        // Add the event author to the recipients, to ensure, 
        // that he receive a notifications.
        $recipients = [$event->getUserID()];
        foreach ($users as $user) {
            if ($event->canRead($user)) {
                $recipients[] = $user->userID;
            }
        }

        return \array_unique($recipients);
    }
}
