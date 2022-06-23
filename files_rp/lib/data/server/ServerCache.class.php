<?php

namespace rp\data\server;

use rp\system\cache\builder\ServerCacheBuilder;
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
 * Manages the server cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Server
 */
class ServerCache extends SingletonFactory
{
    /**
     * cached server ids with server identifier as key
     * @var int[]
     */
    protected $cachedIdentifier;

    /**
     * cached servers
     * @var Server[]
     */
    protected $cachedServers = [];

    /**
     * Returns the server with the given server id or `null` if no such server exists.
     */
    public function getServerByID(int $serverID): ?Server
    {
        return $this->cachedServers[$serverID] ?? null;
    }

    /**
     * Returns the server with the given server identifier or `null` if no such server exists.
     */
    public function getServerByIdentifier(string $identifier): ?Server
    {
        return $this->getServerByID($this->cachedIdentifier[$identifier] ?? 0);
    }

    /**
     * Returns all servers.
     * 
     * @return  Server[]
     */
    public function getServers(): array
    {
        return $this->cachedServers;
    }

    /**
     * Returns the server with the given server id.
     * 
     * @return	Server[]
     */
    public function getServersByID(array $serverIDs): array
    {
        $returnValues = [];

        foreach ($serverIDs as $serverID) {
            $returnValues[] = $this->getServerByID($serverID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedServers = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'server');
        $this->cachedIdentifier = ServerCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'identifier');
    }
}
