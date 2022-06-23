<?php

namespace rp\data\server;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
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
 * Represents a server.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Server
 * 
 * @property-read   int         $serverID               unique id of the server
 * @property-read   int         $packageID              id of the package which delivers the server
 * @property-read   int         $gameID                 id of the game
 * @property-read   string      $identifier             unique textual identifier of the identifier
 * @property-read   string      $type                   type of the server
 * @property-read   int         $serverGroup            group of the server
 */
class Server extends DatabaseObject implements ITitledObject
{

    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns language group name.
     */
    public function getGroupName(): string
    {
        return WCF::getLanguage()->get('rp.server.' . $this->getGame()->identifier . '.group.' . $this->serverGroup);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.server.' . $this->getGame()->identifier . '.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
