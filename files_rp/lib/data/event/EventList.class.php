<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectList;

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
 * Represents a list of events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 *
 * @method      Event           current()
 * @method      Event[]         getObjects()
 * @method      Event|null      search($objectID)
 * @property    Event[]         $objects
 */
class EventList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Event::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'event.startTime';

}
