<?php

namespace rp\acp\page;

use rp\data\rank\RankList;
use wcf\page\MultipleLinkPage;
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
 * Shows the list of ranks.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Page
 *
 * @property    RankList    $objectList
 */
class RankListPage extends MultipleLinkPage
{

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.rank.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ENABLE_RANK'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = RankList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder ASC, rankID ASC';

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('rank.gameID = ?', [RP_DEFAULT_GAME_ID]);
    }
}
