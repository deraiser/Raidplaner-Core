<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarDecorator;
use rp\data\character\avatar\DefaultCharacterAvatar;
use rp\data\character\avatar\ICharacterAvatar;
use rp\data\game\Game;
use rp\data\game\GameCache;
use rp\data\rank\Rank;
use rp\data\rank\RankCache;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledLinkObject;
use wcf\system\event\EventHandler;
use wcf\system\exception\ImplementationException;
use wcf\system\user\storage\UserStorageHandler;
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
     * character avatar
     */
    protected ?CharacterAvatarDecorator $avatar = null;

    /**
     * @inheritDoc
     */
    protected static $baseClass = Character::class;

    /**
     * rank object
     */
    protected ?Rank $rank = null;

    /**
     * Returns a HTML anchor link pointing to the decorated character.
     */
    public function getAnchorTag(): string
    {
        return '<a href="' . $this->getLink() . '" class="rpCharacterLink" data-object-id="' . $this->getObjectID() . '">' . StringUtil::encodeHTML($this->getTitle()) . '</a>';
    }

    /**
     * Returns the character's avatar.
     */
    public function getAvatar(): CharacterAvatarDecorator
    {
        if ($this->avatar === null) {
            $avatar = null;
            
            if ($this->avatarID) {
                if (!$this->fileHash) {
                    $avatars = [];

                    if ($this->userID) {
                        $data = UserStorageHandler::getInstance()->getField('charactersAvatar', $this->userID);
                        if ($data !== null) {
                            $avatars = \unserialize($data);
                        }

                        if (isset($avatars[$this->characterID])) {
                            $avatar = $avatars[$this->characterID];
                        } else {
                            $avatar = new CharacterAvatar($this->avatarID);

                            $avatars[$this->characterID] = $avatar;
                            UserStorageHandler::getInstance()->update(
                                $this->userID,
                                'charactersAvatar',
                                \serialize($avatars)
                            );
                        }
                    } else {
                        $avatar = new CharacterAvatar($this->avatarID);
                    }
                } else {
                    $avatar = new CharacterAvatar(null, $this->getDecoratedObject()->data);
                }
            } else {
                $parameters = ['avatar' => null];
                EventHandler::getInstance()->fireAction($this, 'getAvatar', $parameters);

                if ($parameters['avatar'] !== null) {
                    if (!($parameters['avatar'] instanceof ICharacterAvatar)) {
                        throw new ImplementationException(
                                \get_class($parameters['avatar']),
                                ICharacterAvatar::class
                        );
                    }

                    $avatar = $parameters['avatar'];
                }
            }

            // use default avatar
            if ($avatar === null) {
                $avatar = new DefaultCharacterAvatar($this->characterName ?: '');
            }

            $this->avatar = new CharacterAvatarDecorator($avatar);
        }

        return $this->avatar;
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
     * Returns a formatted title based on the rank.
     */
    public function getFormatedTitle(): string
    {
        if ($this->getRank() === null) return $this->getTitle();
        return $this->getRank()->prefix . $this->getTitle() . $this->getRank()->suffix;
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
     * Returns rank object.
     */
    public function getRank(): ?Rank
    {
        if ($this->rank === null && $this->rankID) {
            $this->rank = RankCache::getInstance()->getRankByID($this->rankID);
        }

        return $this->rank;
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
