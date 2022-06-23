<?php

namespace rp\system\cache\builder;

use rp\data\server\Server;
use wcf\system\cache\builder\AbstractCacheBuilder;
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
 * Caches the server.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class ServerCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'group' => [],
            'identifier' => [],
            'server' => [],
        ];

        // get game server
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_server
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Server $object */
        while ($object = $statement->fetchObject(Server::class)) {
            if (!isset($data['group'][$object->serverGroup])) $data['group'][$object->serverGroup] = [];
            $data['group'][$object->serverGroup][] = $object->serverID;

            $data['identifier'][$object->identifier] = $object->serverID;

            $data['server'][$object->serverID] = $object;
        }

        return $data;
    }
}
