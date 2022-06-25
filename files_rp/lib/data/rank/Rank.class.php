<?php

namespace rp\data\rank;

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
 * Represents a rank.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Rank
 * 
 * @property-read   int         $rankID         unique id of the rank
 * @property-read   string      $rankName       name of the rank
 * @property-read   int         $gameID         id of the game
 * @property-read   string      $prefix         prefix of the rank
 * @property-read   string      $suffix         suffix of the rank
 * @property-read   int         $showOrder      position of the rank in relation to game id
 * @property-read   int         $isDefault      is `1` if the rank is the default rank for this game, otherwise `0`
 */
class Rank extends DatabaseObject implements ITitledObject
{

    /**
     * Returns game object.
     */
    public function getGame(): Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * Returns the rank with the given rank name.
     */
    public static function getRankByRankname(string $name): Rank
    {
        $sql = "SELECT      *
                FROM        rp" . WCF_N . "_rank
                WHERE       rankName = ?
                    AND     gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $name,
            RP_DEFAULT_GAME_ID,
        ]);
        $row = $statement->fetchArray();
        if (!$row) $row = [];

        return new Rank(null, $row);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->rankName;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
