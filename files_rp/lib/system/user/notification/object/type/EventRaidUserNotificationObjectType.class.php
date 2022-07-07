<?php

namespace rp\system\user\notification\object\type;

use rp\data\event\Event;
use rp\data\event\EventList;
use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\system\user\notification\object\type\AbstractUserNotificationObjectType;

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
 * Represents a event raid as a notification object type.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 */
class EventRaidUserNotificationObjectType extends AbstractUserNotificationObjectType
{
    /**
     * @inheritDoc
     */
    protected static $decoratorClassName = EventRaidUserNotificationObject::class;

    /**
     * @inheritDoc
     */
    protected static $objectClassName = Event::class;

    /**
     * @inheritDoc
     */
    protected static $objectListClassName = EventList::class;

}
