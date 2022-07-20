<?php

namespace rp\acp\page;

use rp\data\raid\group\I18nRaidGroupList;
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
 * Shows a list of raid groups.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Page
 *
 * @property	I18nRaidGroupList   $objectList
 */
class RaidGroupListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.group.list';

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'groupNameI18n';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidGroup'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidGroupList::class;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['groupID', 'groupNameI18n', 'members'];

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if (!empty($this->objectList->sqlSelects)) {
            $this->objectList->sqlSelects .= ',';
        }
        $this->objectList->sqlSelects .= "(
            SELECT  COUNT(*)
            FROM    rp" . WCF_N . "_member_to_raid_group
            WHERE   groupID = raid_group.groupID
        ) AS members";
    }

    /**
     * @inheritDoc
     */
    protected function readObjects(): void
    {
        $this->sqlOrderBy = (($this->sortField != 'members' && $this->sortField != 'groupNameI18n') ? 'raid_group.' : '') . $this->sortField . " " . $this->sortOrder;

        parent::readObjects();
    }
}
