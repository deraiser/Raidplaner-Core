<?php

namespace rp\system\cache\builder;

use rp\data\character\CharacterProfile;
use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
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
 * Cached the points of the primary character and its twinks.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class CharacterPointCacheBuilder extends AbstractCacheBuilder
{

    /**
     * Returns the default data.
     */
    protected function getDefaultData(): array
    {
        return [
            'received' => [
                'color' => '',
                'points' => 0
            ],
            'issued' => [
                'color' => '',
                'points' => 0
            ],
            'adjustments' => [
                'color' => '',
                'points' => 0
            ],
            'current' => [
                'color' => '',
                'points' => 0
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [];

        $primaryCharacterID = $parameters['primaryCharacterID'];
        $primaryCharacter = CharacterRuntimeCache::getInstance()->getObject($primaryCharacterID);
        $pointAccounts = PointAccountCache::getInstance()->getPointAccounts();

        $characters = $primaryCharacter->getOtherCharacters();
        $characters[$primaryCharacterID] = $primaryCharacter;

        $characterIDs = \array_keys($characters);

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('characterID IN (?)', [$characterIDs]);
        $sql = "SELECT      characterID, pointAccountID, SUM(points) as points
                FROM        rp" . WCF_N . "_item_to_raid
                        " . $conditionBuilder . "
                GROUP BY    characterID, pointAccountID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditionBuilder->getParameters());
        $characterItems = [];
        while ($row = $statement->fetchArray()) {
            if (!isset($characterItems[$row['characterID']])) $characterItems[$row['characterID']] = [];
            $characterItems[$row['characterID']][$row['pointAccountID']] = $row['points'];
        }

        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('attendee.characterID IN (?)', [$characterIDs]);
        $sql = "SELECT      SUM(raid.points) as points, raid_event.pointAccountID, attendee.characterID
                FROM        rp" . WCF_N . "_raid_attendee attendee
                LEFT JOIN   rp" . WCF_N . "_raid raid
                    ON      (raid.raidID = attendee.raidID)
                LEFT JOIN   rp" . WCF_N . "_raid_event raid_event
                    ON      (raid.raidEventID = raid_event.eventID)
                        " . $conditionBuilder . "
                GROUP BY    attendee.characterID, raid_event.pointAccountID";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditionBuilder->getParameters());
        $characterPoints = [];
        while ($row = $statement->fetchArray()) {
            if (!isset($characterPoints[$row['characterID']])) $characterPoints[$row['characterID']] = [];
            $characterPoints[$row['characterID']][$row['pointAccountID']] = $row['points'];
        }

        /** @var CharacterProfile $character */
        foreach ($characters as $character) {
            $data[$character->characterID] = [];

            /** @var PointAccount $pointAccount */
            foreach ($pointAccounts as $pointAccount) {
                $data[$character->characterID][$pointAccount->pointAccountID] = $this->getDefaultData();

                $points = $characterPoints[$character->characterID][$pointAccount->pointAccountID] ?? 0;
                $data[$character->characterID][$pointAccount->pointAccountID]['received']['points'] = $points;
                if ($points > 0) $data[$character->characterID][$pointAccount->pointAccountID]['received']['color'] = 'green';
                  
                $issuedPoints = $characterItems[$character->characterID][$pointAccount->pointAccountID] ?? 0;
                if ($issuedPoints > 0) {
                    $data[$character->characterID][$pointAccount->pointAccountID]['issued']['color'] = 'red';
                    $data[$character->characterID][$pointAccount->pointAccountID]['issued']['points'] = $issuedPoints;
                }

                $current = $points - $issuedPoints;
                if ($current < 0) $data[$character->characterID][$pointAccount->pointAccountID]['current']['color'] = 'red';
                elseif ($current > 0) $data[$character->characterID][$pointAccount->pointAccountID]['current']['color'] = 'green';
                $data[$character->characterID][$pointAccount->pointAccountID]['current']['points'] = $current;
            }
        }

        return $data;
    }
}
