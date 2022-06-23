<?php

namespace rp\data\character;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\util\StringUtil;

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
 * Decorates the character object and provides functions to retrieve data for character profiles.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 * 
 * @method      Character       getDecoratedObject()
 * @mixin       Character
 */
class CharacterProfile extends DatabaseObjectDecorator implements ITitledLinkObject
{
    const GENDER_MALE = 1;

    const GENDER_FEMALE = 2;

    const GENDER_OTHER = 3;

    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;

    /**
     * Returns a HTML anchor link pointing to the decorated character.
     */
    public function getAnchorTag(): string
    {
        return '<a href="' . $this->getLink() . '" class="rpCharacterLink" data-object-id="' . $this->getObjectID() . '">' . StringUtil::encodeHTML($this->getTitle()) . '</a>';
    }

    /**
     * Returns the character profil with the given character name.
     */
    public static function getCharacterProfilByCharactername(string $name): CharacterProfile
    {
        $character = Character::getCharacterByCharactername($name);
        return new CharacterProfile($character);
    }

    /**
     * Returns game object.
     */
    public function getGame(): Game
    {
        return GameCache::getInstance()->getGameByID($this->gameID);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getDecoratedObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getDecoratedObject()->getTitle();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getDecoratedObject()->__toString();
    }
}
