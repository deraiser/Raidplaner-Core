<?php

namespace rp\system\menu\character\profile\content;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\raid\stats\RaidStatsCache;
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
 * Handles character profile points content.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class PointsCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $character = CharacterRuntimeCache::getInstance()->getObject($characterID);
        if ($character === null) return '';

        $primaryCharacter = $character->getPrimaryCharacter();
        $characterPoints = CharacterPointCacheBuilder::getInstance()->getData(['primaryCharacterID' => $primaryCharacter->characterID]);
        if (!isset($characterPoints[$characterID])) return '';
        $characterPoints = $characterPoints[$characterID];

        $pointAccounts = PointAccountCache::getInstance()->getPointAccounts();
        $raidStats = RaidStatsCache::getInstance()->getStats();

        $sql = "SELECT      raid.date, raidEvent.pointAccountID
                FROM        rp" . WCF_N . "_raid raid
                LEFT JOIN   rp" . WCF_N . "_raid_attendee attendee
                ON          raid.raidID = attendee.raidID
                LEFT JOIN   rp" . WCF_N . "_raid_event raidEvent
                ON          raid.raidEventID = raidEvent.eventID
                WHERE       attendee.characterID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$characterID]);
        $characterStats = [];
        $raidDates = $statement->fetchMap('pointAccountID', 'date', false);
        foreach ($pointAccounts as $pointAccountID => $pointAccount) {
            $characterStats[$pointAccountID] = [];
            foreach (['raid30', 'raid60', 'raid90', 'raidAll'] as $type) {
                $characterStats[$pointAccountID][$type] = [
                    'color' => 'red',
                    'is' => 0,
                    'max' => $raidStats[$pointAccountID][$type] ?? 0,
                    'percent' => 0,
                ];
            };

            if (isset($raidDates[$pointAccountID])) {
                foreach ($raidDates[$pointAccountID] as $date) {
                    $characterStats[$pointAccountID]['raidAll']['is']++;
                    if ($date >= (TIME_NOW - (90 * 86400))) $characterStats[$pointAccountID]['raid90']['is']++;
                    if ($date >= (TIME_NOW - (60 * 86400))) $characterStats[$pointAccountID]['raid60']['is']++;
                    if ($date >= (TIME_NOW - (30 * 86400))) $characterStats[$pointAccountID]['raid30']['is']++;
                }
            }

            foreach (['raid30', 'raid60', 'raid90', 'raidAll'] as $type) {
                if (!$characterStats[$pointAccountID][$type]['is'] &&
                    !$characterStats[$pointAccountID][$type]['max']) {
                    continue;
                }

                $characterStats[$pointAccountID][$type]['percent'] = \number_format(
                    ($characterStats[$pointAccountID][$type]['is'] /
                    $characterStats[$pointAccountID][$type]['max']) * 100
                );

                if ($characterStats[$pointAccountID][$type]['percent'] >= 40 &&
                    $characterStats[$pointAccountID][$type]['percent'] < 80) {
                    $characterStats[$pointAccountID][$type]['color'] = 'yellow';
                } else if ($characterStats[$pointAccountID][$type]['percent'] >= 80) {
                    $characterStats[$pointAccountID][$type]['color'] = 'green';
                }
            }

            foreach (['adjustments', 'current', 'issued', 'received'] as $type) {
                $characterPoints[$pointAccountID][$type]['points'] = RPUtil::formatPoints($characterPoints[$pointAccountID][$type]['points']);
            }
        }

        \usort($pointAccounts, static function (PointAccount $a, PointAccount $b) {
            return \strcasecmp($a->getTitle(), $b->getTitle());
        });

        WCF::getTPL()->assign([
            'characterPoints' => $characterPoints,
            'characterStats' => $characterStats,
            'pointAccounts' => $pointAccounts,
        ]);

        return WCF::getTPL()->fetch('characterProfilePoints', 'rp');
    }

    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
