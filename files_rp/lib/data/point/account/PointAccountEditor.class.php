<?php

namespace rp\data\point\account;

use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\system\cache\builder\PointAccountCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
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
 * Provides functions to edit point account.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Point\Account
 * 
 * @method static   PointAccount    create(array $parameters = [])
 * @method          PointAccount    
 */
class PointAccountEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PointAccount::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        PointAccountCacheBuilder::getInstance()->reset();
        CharacterPointCacheBuilder::getInstance()->reset();
    }

    /**
     * Adds the point account to a specific position.
     */
    public function setShowOrder(int $showOrder = 0): void
    {
        // shift back point accounts with higher showOrder
        if ($this->showOrder) {
            $sql = "UPDATE  rp" . WCF_N . "_point_account
                    SET     showOrder = showOrder - 1
                    WHERE   gameID = ?
                        AND showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->gameID, $this->showOrder]);
        }

        // shift point accounts with higher showOrder
        if ($showOrder) {
            $sql = "UPDATE  rp" . WCF_N . "_point_account
                    SET     showOrder = showOrder + 1
                    WHERE   gameID = ?
                        AND showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->gameID, $showOrder]);
        }

        // get maximum existing show order
        $sql = "SELECT  MAX(showOrder)
                FROM    rp" . WCF_N . "_point_account
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->gameID]);
        $maxShowOrder = $statement->fetchSingleColumn() ?: 0;

        if (!$showOrder || $showOrder > $maxShowOrder) {
            $showOrder = $maxShowOrder + 1;
        }

        $this->update(['showOrder' => $showOrder]);
    }
}
