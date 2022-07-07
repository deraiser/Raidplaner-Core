<?php

namespace rp\system\user\notification\object;

use rp\data\event\Event;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\user\notification\object\IUserNotificationObject;

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
 * Represents a event as a notification object type.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object
 *
 * @method      Event       getDecoratedObject()
 * @mixin       Event
 */
class EventRaidUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Event::class;

    /**
     * @inheritDoc
     */
    public function getAuthorID(): int
    {
        return $this->getDecoratedObject()->userID;
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
}
