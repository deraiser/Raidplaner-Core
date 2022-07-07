<?php

use rp\data\game\GameCache;
use wcf\data\option\OptionEditor;
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
 * @author      Marco Daries
 * @package     Daries\RP
 */
// set default game
$sql = "SELECT  gameID
        FROM    rp" . WCF_N . "_game
        WHERE   identifier = ?";
$statement = WCF::getDB()->prepareStatement($sql, 1);
$statement->execute(['default']);
$gameID = $statement->fetchSingleColumn();

$sql = "UPDATE  wcf" . WCF_N . "_option
        SET     optionValue = ?
        WHERE   optionName = ?";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute([
    $gameID,
    'rp_default_game_id',
]);

// update options.inc.php
OptionEditor::resetCache();

// Default Rank
$sql = "INSERT INTO rp" . WCF_N . "_rank
                    (rankName, gameID, showOrder, isDefault)
        VALUES      (?, ?, ?, ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute([
    'Default',
    GameCache::getInstance()->getGameByIdentifier('default')->gameID,
    1,
    1,
]);

// Default Point Account
$sql = "INSERT INTO rp" . WCF_N . "_point_account
                    (pointAccountName, description, gameID, showOrder)
        VALUES      (?, ?, ?, ?)";
$statement = WCF::getDB()->prepareStatement($sql);
$statement->execute([
    'Default',
    'Default-Pool',
    GameCache::getInstance()->getGameByIdentifier('default')->gameID,
    1,
]);
