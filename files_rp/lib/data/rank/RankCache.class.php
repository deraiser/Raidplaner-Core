<?php

namespace rp\data\rank;

use rp\system\cache\builder\RankCacheBuilder;
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
 * Manages the rank cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Rank
 */
class RankCache extends SingletonFactory
{
    /**
     * cached default rank
     */
    protected int $cachedDefault = 0;

    /**
     * cached ranks
     * @var Rank[]
     */
    protected array $cachedRanks = [];

    /**
     * Retrieves the default rank.
     * May return `null` if there is no default rank.
     */
    public function getDefaultRank(): ?Rank
    {
        return $this->getRankByID($this->cachedDefault);
    }

    /**
     * Returns the rank with the given rank id or `null` if no such rank exists.
     */
    public function getRankByID(int $rankID): ?Rank
    {
        return $this->cachedRanks[$rankID] ?? null;
    }

    /**
     * Returns all ranks.
     * 
     * @return  Rank[]
     */
    public function getRanks(): array
    {
        return $this->cachedRanks;
    }

    /**
     * Returns the rank with the given rank id.
     */
    public function getRanksByID(array $rankIDs): array
    {
        $returnValues = [];

        foreach ($rankIDs as $rankID) {
            $returnValues[] = $this->getRankByID($rankID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedDefault = RankCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'default');
        $this->cachedRanks = RankCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'ranks');
    }
}
