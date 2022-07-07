<?php

namespace rp\acp\page;

use rp\data\point\account\PointAccountList;
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
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Page
 *
 * @property    PointAccountList    $objectList
 */
class PointAccountListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ITEM_ACCOUNT_EASYMODE_DISABLED'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = PointAccountList::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder ASC, pointAccountID ASC';

}
