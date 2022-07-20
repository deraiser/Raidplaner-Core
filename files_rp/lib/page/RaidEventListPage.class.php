<?php

namespace rp\page;

use rp\data\raid\event\I18nRaidEventList;
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
 * Shows the raid events page.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Page
 */
class RaidEventListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = I18nRaidEventList::class;

    /**
     * @inheritDoc
     */
    public $sortField = 'eventNameI18n';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

}
