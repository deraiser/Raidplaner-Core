<?php

namespace rp\data\modification\log;

use rp\system\log\modification\EventModificationLogHandler;
use wcf\data\modification\log\ModificationLogList;

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
 * Represents a list of modification logs for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Modification\Log
 *
 * @method      ViewableEventModificationLog        current()
 * @method      ViewableEventModificationLog[]      getObjects()
 * @method      ViewableEventModificationLog|null   search($objectID)
 * @property    ViewableEventModificationLog[]      $objects
 */
class EventModificationLogList extends ModificationLogList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableEventModificationLog::class;

    /**
     * Initializes the event modification log list.
     */
    public function setEventData(array $eventIDs, string $action = '')
    {
        $this->getConditionBuilder()->add("objectTypeID = ?", [EventModificationLogHandler::getInstance()->getObjectType()->objectTypeID]);
        $this->getConditionBuilder()->add("objectID IN (?)", [$eventIDs]);
        if (!empty($action)) {
            $this->getConditionBuilder()->add("action = ?", [$action]);
        }
    }
}
