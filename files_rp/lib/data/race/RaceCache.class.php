<?php

namespace rp\data\race;

use rp\system\cache\builder\RaceCacheBuilder;
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
 * Manages the race cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Race
 */
class RaceCache extends SingletonFactory
{
    /**
     * cached race ids with race identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached races
     * @var Race[]
     */
    protected $cachedRaces = [];

    /**
     * Returns the race with the given race id or `null` if no such race exists.
     */
    public function getRaceByID(int $raceID): ?Race
    {
        return $this->cachedRaces[$raceID] ?? null;
    }

    /**
     * Returns the race with the given race identifier or `null` if no such race exists.
     */
    public function getRaceByIdentifier(string $identifier): ?Race
    {
        return $this->getRaceByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all races.
     * 
     * @return	Race[]
     */
    public function getRaces(): array
    {
        return $this->cachedRaces;
    }

    /**
     * Returns the race with the given race id.
     * 
     * @return	Race[]
     */
    public function getRacesByID(array $raceIDs): array
    {
        $returnValues = [];

        foreach ($raceIDs as $raceID) {
            $returnValues[] = $this->getRaceByID($raceID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedIdentifier = RaceCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
        $this->cachedRaces = RaceCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'race');
    }
}
