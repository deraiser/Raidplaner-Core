<?php

namespace rp\data\game;

use wcf\data\AbstractDatabaseObjectAction;
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
 * Executes game related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Game
 * 
 * @method      GameEditor[]    getObjects()
 * @method      GameEditor      getSingleObject()
 */
class GameAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    public function delete()
    {
        $return = parent::delete();

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
        
        return $return;
    }
}
