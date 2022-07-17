<?php

namespace rp\system\character\point;

use rp\data\character\CharacterProfile;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\util\RPUtil;
use wcf\system\SingletonFactory;

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
 * Handles character points.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Character\Point
 */
class CharacterPointHandler extends SingletonFactory
{
    /**
     * loaded character points
     */
    protected array $characterPoints = [];

    public function getPoints(CharacterProfile $characterProfile): array
    {
        $primaryCharacter = $characterProfile->getPrimaryCharacter();
        $this->loadCharacterPoints($primaryCharacter);

        if (RP_SHOW_TWINKS) $datas = $this->characterPoints[$primaryCharacter->characterID][$characterProfile->characterID] ?? [];
        $datas = $this->characterPoints[$primaryCharacter->characterID] ?? [];
        
        return $datas;
    }

    /**
     * Loads primary character points and twinks.
     */
    protected function loadCharacterPoints(CharacterProfile $primaryCharacter): void
    {
        $primaryCharacterID = $primaryCharacter->characterID;

        if (!isset($this->characterPoints[$primaryCharacterID])) {
            $datas = CharacterPointCacheBuilder::getInstance()->getData(['primaryCharacterID' => $primaryCharacterID]);

            if (RP_SHOW_TWINKS) $this->characterPoints[$primaryCharacterID] = $datas;
            else {
                $newDatas = [];
                foreach ($datas as $characterID => $pointAccounts) {
                    foreach ($pointAccounts as $pointAccountID => $data) {
                        if (!isset($newDatas[$pointAccountID])) {
                            $newDatas[$pointAccountID] = $data;
                            continue;
                        }

                        $newDatas[$pointAccountID]['received']['points'] += $data['received']['points'];
                        if ($newDatas[$pointAccountID]['received']['points'] > 0) $newDatas[$pointAccountID]['received']['color'] = 'green';
                        
                        $newDatas[$pointAccountID]['adjustments']['points'] += $data['adjustments']['points'];
                        if ($newDatas[$pointAccountID]['adjustments']['points'] > 0) $newDatas[$pointAccountID]['adjustments']['color'] = 'red';

                        if ($data['issued']['points'] > 0) {
                            $newDatas[$pointAccountID]['issued']['points'] += $data['issued']['points'];
                            $newDatas[$pointAccountID]['issued']['color'] = 'red';
                        }

                        $current = $newDatas[$pointAccountID]['received']['points'] - $newDatas[$pointAccountID]['issued']['points'];
                        $newDatas[$pointAccountID]['current']['points'] = $current;

                        if ($newDatas[$pointAccountID]['current']['points'] < 0) $newDatas[$pointAccountID]['current']['color'] = 'red';
                        elseif ($newDatas[$pointAccountID]['current']['points'] > 0) $newDatas[$pointAccountID]['current']['color'] = 'green';
                    }
                }

                $this->characterPoints[$primaryCharacterID] = $newDatas;
            }
        }
    }
}
