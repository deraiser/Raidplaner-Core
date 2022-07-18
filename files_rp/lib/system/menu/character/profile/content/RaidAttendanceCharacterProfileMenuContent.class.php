<?php

namespace rp\system\menu\character\profile\content;

use rp\data\raid\event\RaidEventCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
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
 * Handles character profile raid attendance content.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */

class RaidAttendanceCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $raidEvents = RaidEventCache::getInstance()->getRaidEvents();

        $sql = "SELECT      raidEventID, COUNT(*) as count
                FROM        rp" . WCF_N . "_raid
                GROUP BY    raidEventID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $maxRaids = $statement->fetchMap('raidEventID', 'count');

        $characterRaids = [];
        $sql = "SELECT  raidID
                FROM    rp" . WCF_N . "_raid_attendee
                WHERE   characterID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterID]);
        $raidIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (!empty($raidIDs)) {
            $conditions = new PreparedStatementConditionBuilder();
            $conditions->add("raidID IN (?)", [$raidIDs]);

            $sql = "SELECT      raidEventID, COUNT(*) as count
                    FROM        rp" . WCF_N . "_raid
                    " . $conditions . "
                    GROUP BY    raidEventID";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());
            $characterRaids = $statement->fetchMap('raidEventID', 'count');
        }

        foreach ($raidEvents as $raidEvent) {
            $raidStats[$raidEvent->eventID] = [
                'is' => $characterRaids[$raidEvent->eventID] ?? 0,
                'max' => $maxRaids[$raidEvent->eventID] ?? 0,
                'percent' => 0
            ];

            if (!$raidStats[$raidEvent->eventID]['is'] && !$raidStats[$raidEvent->eventID]['max']) $raidStats[$raidEvent->eventID]['percent'] = 0;
            else $raidStats[$raidEvent->eventID]['percent'] = \number_format(($raidStats[$raidEvent->eventID]['is'] / $raidStats[$raidEvent->eventID]['max']) * 100);
        }

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'raidEvents' => $raidEvents,
            'raidStats' => $raidStats
        ]);

        return WCF::getTPL()->fetch('characterProfileRaidAttendance', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
