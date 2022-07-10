<?php

namespace rp\data\event;

use wcf\data\like\Like;
use wcf\data\like\object\AbstractLikeObject;
use wcf\data\reaction\object\IReactionObject;
use wcf\system\user\notification\object\LikeUserNotificationObject;
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
 * Likeable object implementation for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 *
 * @method      Event   getDecoratedObject()
 * @mixin       Event
 */
class LikeableEvent extends AbstractLikeObject implements IReactionObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

    /**
     * @inheritDoc
     */
    public function getLanguageID(): ?int
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getObjectID(): int
    {
        return $this->eventID;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function getURL(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getUserID(): int
    {
        return $this->userID;
    }

    /**
     * @inheritDoc
     */
    public function sendNotification(Like $like): void
    {
        if ($this->getDecoratedObject()->userID != WCF::getUser()->userID) {
            $notificationObject = new LikeUserNotificationObject($like);
            UserNotificationHandler::getInstance()->fireEvent(
                'like',
                'info.daries.rp.likeableEvent.notification',
                $notificationObject,
                [$this->getDecoratedObject()->userID],
                ['objectID' => $this->getDecoratedObject()->entryID]
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function updateLikeCounter($cumulativeLikes): void
    {
        // update cumulative likes
        $editor = new EventEditor($this->getDecoratedObject());
        $editor->update(['cumulativeLikes' => $cumulativeLikes]);
    }
}
