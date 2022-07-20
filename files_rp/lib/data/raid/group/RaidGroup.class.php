<?php

namespace rp\data\raid\group;

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
 * Represents a raid group.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Group
 *
 * @property-read   int     $groupID            unique id of the raid group
 * @property-read   string  $groupName          name of the raid group or name of language item which contains the name
 * @property-read   string  $groupDescription   description of the raid group or name of language item which contains the description
 */
class RaidGroup extends DatabaseObject implements ITitledObject
{

    /**
     * Returns the raid group description in the active user's language.
     */
    public function getDescription(): string
    {
        return WCF::getLanguage()->get($this->groupDescription);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->groupName);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
