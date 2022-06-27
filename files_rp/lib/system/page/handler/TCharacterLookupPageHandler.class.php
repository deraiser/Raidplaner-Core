<?php

namespace rp\system\page\handler;

use rp\data\character\CharacterProfileList;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\system\page\handler\ILookupPageHandler;

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
 * Provides the `isValid` and `lookup` methods for looking up characters.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Core\System\Page\Handler
 */
trait TCharacterLookupPageHandler
{

    /**
     * Returns true if provided object id exists and is valid.
     *
     * @param   int $objectID   page object id
     * @see     ILookupPageHandler::isValid()
     */
    public function isValid($objectID): bool
    {
        return CharacterRuntimeCache::getInstance()->getObject($objectID) !== null;
    }

    /**
     * Performs a search for pages using a query string, returning an array containing
     * an `objectID => title` relation.
     *
     * @param   string      $searchString search string
     * @return  string[]
     * @see     ILookupPageHandler::lookup()
     */
    public function lookup($searchString): array
    {
        $characterList = new CharacterProfileList();
        $characterList->getConditionBuilder()->add('member.characterName LIKE ?', ['%' . $searchString . '%']);
        $characterList->readObjects();

        $results = [];
        foreach ($characterList as $character) {
            $results[] = [
                'image' => $character->getAvatar()->getImageTag(48),
                'link' => $this->getLink($character->characterID),
                'objectID' => $character->characterID,
                'title' => $character->characterName,
            ];
        }

        return $results;
    }
}
