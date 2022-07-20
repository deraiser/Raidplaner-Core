<?php

namespace rp\data\raid\group;

use rp\system\cache\builder\RaidGroupCacheBuilder;
use wcf\system\SingletonFactory;
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
 * Manages the raid group cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Group
 */
class RaidGroupCache extends SingletonFactory
{

    /**
     * cached raid groups
     * @var RaidGroup[]
     */
    protected array $cachedGroups = [];

    /**
     * cached character ids in raid groups
     */
    protected array $cachedMembers = [];

    /**
     * Returns the raid group with the given group id or `null` if no such raid group exists.
     */
    public function getGroupByID(int $groupID): ?RaidGroup
    {
        return $this->cachedGroups[$groupID] ?? null;
    }

    /**
     * Returns all raid groups.
     * 
     * @return	RaidGroup[]
     */
    public function getGroups(): array
    {
        return $this->cachedGroups;
    }

    /**
     * Returns the raid groups with the given group ids.
     * 
     * @return	RaidGroup[]
     */
    public function getGroupsByID(array $groupIDs): array
    {
        $returnValues = [];

        foreach ($groupIDs as $groupID) {
            $returnValues[] = $this->getGroupByID($groupID);
        }

        return $returnValues;
    }

    /**
     * Returns all member ids based on the group id.
     */
    public function getMembersIDsByGroupID(int $groupID): array
    {
        return $this->cachedMembers[$groupID] ?? [];
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedGroups = RaidGroupCacheBuilder::getInstance()->getData(['languageID' => WCF::getLanguage()->languageID], 'groups');
        $this->cachedMembers = RaidGroupCacheBuilder::getInstance()->getData(['languageID' => WCF::getLanguage()->languageID], 'members');
    }

}
