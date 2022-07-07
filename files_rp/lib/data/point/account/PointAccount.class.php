<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
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
 * Represents a point account.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Point\Account
 * 
 * @property-read   int         $pointAccountID     unique id of the point account
 * @property-read   string      $pointAccountName   name of the point account or name of language item which contains the name
 * @property-read   string      $description        description of the point account
 * @property-read   int         $gameID             id of the game
 * @property-read   int         $showOrder          position of the point account
 */
class PointAccount extends DatabaseObject implements ITitledObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'pointAccountID';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->pointAccountName);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
