<?php

namespace rp\data\race;

use rp\system\cache\builder\RaceCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

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
 * Provides functions to edit race.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Race
 * 
 * @method static   Race    create(array $parameters = [])
 * @method          Race    getDecoratedObject()
 * @mixin           Race
 */
class RaceEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Race::class;

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        RaceCacheBuilder::getInstance()->reset();
    }
}
