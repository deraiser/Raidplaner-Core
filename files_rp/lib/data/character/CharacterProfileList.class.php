<?php

namespace rp\data\character;

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
 * Represents a list of character profiles.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 *
 * @method      CharacterProfile        current()
 * @method      CharacterProfile[]      getObjects()
 * @method      CharacterProfile|null   search($objectID)
 * @property    CharacterProfile[]      $objects
 */
class CharacterProfileList extends CharacterList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'characterName';

    /**
     * @inheritDoc
     */
    public $decoratorClassName = CharacterProfile::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects .= "member_avatar.*";
        $this->sqlJoins .= "
            LEFT JOIN   rp" . WCF_N . "_member_avatar member_avatar
            ON          member_avatar.avatarID = member.avatarID";

        if (RP_ENABLE_RANK) {
            $this->sqlSelects .= ",rank.*";
            $this->sqlJoins .= "
                LEFT JOIN   rp" . WCF_N . "_rank rank
                ON          rank.rankID = member.rankID";
        }
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        if ($this->objectIDs === null) {
            $this->readObjectIDs();
        }

        parent::readObjects();
    }
}
