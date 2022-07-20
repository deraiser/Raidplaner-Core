<?php

namespace rp\acp\page;

use rp\data\raid\event\I18nRaidEventList;
use wcf\page\SortablePage;

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
 * Shows a list of raid events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Page
 *
 * @property	I18nRaidEventList   $objectList
 */
class RaidEventListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.list';

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'eventNameI18n';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 50;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['defaultPoints', 'pointAccountName', 'eventID', 'eventNameI18n'];

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if (!empty($this->objectList->sqlSelects)) {
            $this->objectList->sqlSelects .= ',';
        }
        $this->objectList->sqlSelects .= 'point_account.pointAccountName';
        $this->objectList->sqlJoins .= " LEFT JOIN rp" . WCF_N . "_point_account point_account ON (point_account.pointAccountID = raid_event.pointAccountID)";
    }
}
