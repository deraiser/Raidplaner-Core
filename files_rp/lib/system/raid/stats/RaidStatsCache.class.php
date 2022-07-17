<?php

namespace rp\system\raid\stats;

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
 * Caches raid stats.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Raid\Stats
 */
class RaidStatsCache extends SingletonFactory
{

    public function getStats(): array
    {
        $stats = [];

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp" . WCF_N . "_raid raid
                LEFT JOIN   rp" . WCF_N . "_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            if (!isset($stats[$row['pointAccountID']])) {
                $stats[$row['pointAccountID']] = [
                    'raid30' => 0,
                    'raid60' => 0,
                    'raid90' => 0,
                    'raidAll' => 0
                ];
            }

            $stats[$row['pointAccountID']]['raidAll']++;
            if ($row['date'] >= (TIME_NOW - (90 * 86400))) $stats[$row['pointAccountID']]['raid90']++;
            if ($row['date'] >= (TIME_NOW - (60 * 86400))) $stats[$row['pointAccountID']]['raid60']++;
            if ($row['date'] >= (TIME_NOW - (30 * 86400))) $stats[$row['pointAccountID']]['raid30']++;
        }

        return $stats;
    }
}
