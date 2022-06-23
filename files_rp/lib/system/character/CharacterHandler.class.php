<?php

namespace rp\system\character;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
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
 * Handles characters for current users.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Character
 */
class CharacterHandler extends SingletonFactory
{
    /**
     * Content of the characters of the current user
     * @var CharacterProfile[]
     */
    protected array $characters = [];

    /**
     * Returns the character with the given character id or `null` if no character with id exists.
     */
    public function getCharacterByID(int $characterID): ?CharacterProfile
    {
        return$this->characters[$characterID] ?? null;
    }

    /**
     * Returns list of user characters.
     *
     * @return  CharacterProfile[]
     */
    public function getCharacters(): array
    {
        return $this->characters;
    }

    /**
     * Returns primary character or `null` if no primary character exists.
     */
    public function getPrimaryCharacter(): ?CharacterProfile
    {
        foreach ($this->getCharacters() as $character) {
            if ($character->isPrimary) {
                return $character;
            }
        }

        return null;
    }

    /**
     * Returns true if the current user has characters.
     */
    public function hasCharacters(): bool
    {
        if (\count($this->getCharacters())) return true;
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        if (WCF::getUser()->userID) {
            $characterList = new CharacterProfileList();
            $characterList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
            $characterList->getConditionBuilder()->add('gameID = ?', [RP_DEFAULT_GAME_ID]);
            $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
            $characterList->readObjects();
            $this->characters = $characterList->getObjects();
        }
    }
}
