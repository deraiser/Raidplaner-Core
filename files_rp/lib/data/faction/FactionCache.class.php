<?php

namespace rp\data\faction;

use rp\system\cache\builder\FactionCacheBuilder;
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
 * Manages the faction cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Faction
 */
class FactionCache extends SingletonFactory
{
    /**
     * cached faction ids with faction identifier as key
     * @var int[]
     */
    protected array $cachedFactionByIdentifier = [];

    /**
     * cached factions
     * @var Faction[]
     */
    protected array $cachedFactions = [];

    /**
     * Returns the faction with the given faction id or `null` if no such faction exists.
     */
    public function getFactionByID(int $factionID): ?Faction
    {
        return $this->cachedFactions[$factionID] ?? null;
    }

    /**
     * Returns the faction with the given faction identifier or `null` if no such faction exists.
     */
    public function getFactionByIdentifier(string $identifier): ?Faction
    {
        return $this->getFactionByID($this->cachedFactionByIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all factions.
     * 
     * @return	Faction[]
     */
    public function getFactions(): array
    {
        return $this->cachedFactions;
    }

    /**
     * Returns the factions with the given faction ids.
     * 
     * @return	Faction[]
     */
    public function getFactionsByIDs(array $factionIDs): array
    {
        $returnValues = [];

        foreach ($factionIDs as $factionID) {
            $returnValues[] = $this->getFactionByID($factionID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedFactions = FactionCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'faction');
        $this->cachedFactionByIdentifier = FactionCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
