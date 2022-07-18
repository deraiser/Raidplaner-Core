<?php

namespace rp\system\menu\character\profile\content;

use rp\data\item\ItemCache;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\RaidRuntimeCache;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;
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
 * Handles character profile item content.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class ItemCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $sql = "SELECT      item_to_raid.*, raid.date
                FROM        rp" . WCF_N . "_item_to_raid item_to_raid
                LEFT JOIN   rp" . WCF_N . "_raid raid
                ON          item_to_raid.raidID = raid.raidID
                WHERE       item_to_raid.characterID = ?
                ORDER BY    raid.date DESC";
        $statement = WCF::getDB()->prepareStatement($sql, 10);
        $statement->execute([$characterID]);

        $items = [];
        while ($row = $statement->fetchArray()) {
            $items[] = [
                'item' => ItemCache::getInstance()->getItemByID($row['itemID']),
                'pointAccount' => PointAccountCache::getInstance()->getPointAccountByID($row['pointAccountID']),
                'points' => RPUtil::formatPoints($row['points']),
                'raid' => RaidRuntimeCache::getInstance()->getObject($row['raidID'])
            ];
        }

        $items = \array_slice($items, 0, 6);

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'items' => $items,
            'lastItemOffset' => 6
        ]);

        return WCF::getTPL()->fetch('characterProfileItem', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        if (!RP_ENABLE_ITEM) return false;

        return true;
    }
}
