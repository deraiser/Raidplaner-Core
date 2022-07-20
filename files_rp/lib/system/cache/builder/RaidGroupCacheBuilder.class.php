<?php

namespace rp\system\cache\builder;

use rp\data\raid\group\I18nRaidGroupList;
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
 * Caches all raid groups.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Cache\Builder
 */
class RaidGroupCacheBuilder extends AbstractCacheBuilder
{

    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $data = [
            'groups' => [],
            'members' => []
        ];

        $sql = "SELECT  characterID, groupID
                FROM    rp" . WCF_N . "_member_to_raid_group";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        $raidMembers = $statement->fetchMap('groupID', 'characterID', false);

        // get all raid groups
        $groupList = new I18nRaidGroupList($parameters['languageID'] ?? null);
        $groupList->sqlOrderBy = 'groupNameI18n ASC';
        $groupList->readObjects();
        foreach ($groupList as $group) {
            $data['groups'][$group->groupID] = $group;
            $data['members'][$group->groupID] = $raidMembers[$group->groupID] ?? [];
        }

        return $data;
    }
}
