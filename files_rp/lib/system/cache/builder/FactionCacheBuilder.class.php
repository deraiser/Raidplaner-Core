<?php

namespace rp\system\cache\builder;

use rp\data\faction\Faction;
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
 * Caches the faction.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class FactionCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = ['faction' => [], 'identifier' => []];

        // get game faction
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_faction
                WHERE   gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$parameters['gameID']]);

        /** @var Faction $object */
        while ($object = $statement->fetchObject(Faction::class)) {
            $data['faction'][$object->factionID] = $object;
            $data['identifier'][$object->identifier] = $object->factionID;
        }

        return $data;
    }
}
