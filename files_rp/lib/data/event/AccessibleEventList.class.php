<?php

namespace rp\data\event;

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
 * Represents a list of accessible events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 */
class AccessibleEventList extends ViewableEventList
{

    /**
     * Creates a new AccessibleEventList object.
     */
    public function __construct(int $start = 0, int $end = 0)
    {
        parent::__construct();

        if ($start && $end) {
            $this->getConditionBuilder()->add('((event.startTime >= ? AND event.startTime < ?) OR (event.startTime < ? AND event.endTime >= ?))', [$start, $end, $start, $start]);
        } else if ($start) {
            $this->getConditionBuilder()->add('(event.startTime >= ? OR (event.startTime <= ? AND event.endTime > ?))', [$start, $start, $start]);
        } else if ($end) {
            $this->getConditionBuilder()->add('event.endTime < ?', [$end]);
        }

        // default conditions
        if (!WCF::getSession()->getPermission('mod.rp.canModerateEvent')) $this->getConditionBuilder()->add('event.isDisabled = ?', [0]);
        if (!WCF::getSession()->getPermission('mod.rp.canViewDeletedEvent')) $this->getConditionBuilder()->add('event.isDeleted = ?', [0]);
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        if ($this->objectIDs === null) $this->readObjectIDs();

        parent::readObjects();
    }
}
