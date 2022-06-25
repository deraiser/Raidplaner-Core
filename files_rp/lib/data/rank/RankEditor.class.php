<?php

namespace rp\data\rank;

use rp\system\cache\builder\RankCacheBuilder;
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
 * Provides functions to edit rank.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Rank
 * 
 * @method static   Rank    create(array $parameters = [])
 * @method          Rank    getDecoratedObject()
 * @mixin           Rank
 */
class RankEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Rank::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RankCacheBuilder::getInstance()->reset();
    }

    /**
     * Sets current rank as default rank.
     */
    public function setAsDefault(): void
    {
        // remove default flag from all ranks
        $sql = "UPDATE	rp" . WCF_N . "_rank
                SET     isDefault = ?
                WHERE	isDefault = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            0,
            1,
            $this->gameID,
        ]);

        // set current rank as default rank
        $this->update(['isDefault' => 1]);

        self::resetCache();
    }
}
