<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObject;

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
 * Represents a item database.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Item\Database
 *
 * @property-read   string      $databaseName   unique name and textual identifier of the item database
 * @property-read   int|null    $packageID      id of the package the which delivers the item database
 * @property-read   string      $className      name of the PHP class implementing `rp\system\item\database\IItemDatabase` handling search handled data
 */
class ItemDatabase extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'databaseName';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexIsIdentity = false;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'item_database';

}
