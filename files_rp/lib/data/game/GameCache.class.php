<?php

namespace rp\data\game;

use rp\system\cache\builder\GameCacheBuilder;
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
 * Manages the game cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Game
 */
class GameCache extends SingletonFactory
{
    /**
     * cached game ids with game identifier as key
     * @var int[]
     */
    protected array $cachedIdentifier = [];

    /**
     * cached games
     * @var Game[]
     */
    protected array $cachedGames = [];

    /**
     * Returns the game with the given game id or `null` if no such game exists.
     */
    public function getGameByID(int $gameID): ?Game
    {
        return $this->cachedGames[$gameID] ?? null;
    }

    /**
     * Returns the game with the given game identifier or `null` if no such game exists.
     */
    public function getGameByIdentifier(string $identifier): ?Game
    {
        if (!isset($this->cachedIdentifier[$identifier])) return null;
        return $this->getGameByID($this->cachedIdentifier[$identifier]);
    }

    /**
     * Returns all games.
     * 
     * @return  Game[]
     */
    public function getGames(): array
    {
        return $this->cachedGames;
    }

    /**
     * Returns the game with the given game ids.
     * 
     * @return	Game[]
     */
    public function getGamesByID(array $gameIDs): array
    {
        $returnValues = [];

        foreach ($gameIDs as $gameID) {
            $returnValues[] = $this->getGameByID($gameID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedGames = GameCacheBuilder::getInstance()->getData([], 'games');
        $this->cachedIdentifier = GameCacheBuilder::getInstance()->getData([], 'identifier');
    }
}
