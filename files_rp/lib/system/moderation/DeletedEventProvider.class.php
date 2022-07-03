<?php

namespace rp\system\moderation;

use rp\data\event\DeletedEventList;
use wcf\system\moderation\AbstractDeletedContentProvider;

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
 * Implementation of IDeletedContentProvider for deleted events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Moderation
 */
class DeletedEventProvider extends AbstractDeletedContentProvider
{

    /**
     * @inheritDoc
     */
    public function getObjectList(): DeletedEventList
    {
        $eventList = new DeletedEventList();
        $eventList->sqlOrderBy = "event.deleteTime DESC, event.eventID DESC";

        return $eventList;
    }

    /**
     * @inheritDoc
     */
    public function getTemplateName(): string
    {
        return 'deletedEventList';
    }
}
