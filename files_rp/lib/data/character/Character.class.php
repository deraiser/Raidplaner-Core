<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\data\IPopoverObject;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
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
 * Represents a character.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 * 
 * @property-read   int         $characterID                unique id of the character
 * @property-read   string      $characterName              name of the character
 * @property-read   int|null    $userID                     id of the user who created the character, or `null` if not already assigned.
 * @property-read   int         $gameID                     id of the game for created the character
 * @property-read   int|null    $rankID                     id of the rank for created the character, or `null` if not already assigned.
 * @property-read   int|null    $avatarID                   id of the character's avatar or null if they have no avatar
 * @property-read   int         $created                    timestamp at which the character has been created
 * @property-read   int         $lastUpdateTime             timestamp at which the character has been updated the last time
 * @property-read   string      $notes                      notes of the character
 * @property-read   int         $notesHasEmbeddedObjects    s `1` if there are embedded objects in the notes, otherwise `0`
 * @property-read   array       $additionalData             array with additional data of the character
 * @property-read   string      $guildName                  guild name if character does not belong to this guild
 * @property-read   int         $profileHits                number of times the character's profile has been visited
 * @property-read   int         $isPrimary                  is `1` if the character is primary character of this game, otherwise `0`
 * @property-read   int         $isDisabled                 is `1` if the character is disabled and thus is not displayed, otherwise `0`
 */
class Character extends DatabaseObject implements IPopoverObject, IRouteController
{
    /**
     * available role ids
     */
    protected ?array $availableRoleIDs = null;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'member';

    /**
     * other characters of the current character
     */
    protected ?array $otherCharacters = null;

    /**
     * user profile object
     */
    protected ?UserProfile $user = null;

    /**
     * Returns true if the active user can delete this character.
     */
    public function canDelete(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canDeleteCharacter')) {
            return true;
        }

        if ($this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canDeleteOwnCharacter')) {
            $characters = self::getAllCharactersByUserID($this->userID);
            if (\count($characters) == 1) return true;
            elseif (!$this->isPrimary) return true;

            return false;
        }

        return false;
    }

    /**
     * Returns true if the active user can edit this character.
     */
    public function canEdit(): bool
    {
        if (WCF::getSession()->getPermission('admin.rp.canEditCharacter')) {
            return true;
        }

        if ($this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canEditOwnCharacter')) {
            $characters = self::getAllCharactersByUserID($this->userID);
            if (\count($characters) == 1) return true;
            elseif (!$this->isPrimary) return true;

            return false;
        }

        return false;
    }

    /**
     * Returns all characters by user id.
     * 
     * @return CharacterProfile[]
     */
    public static function getAllCharactersByUserID(int $userID): array
    {
        $sql = "SELECT      *
                FROM        rp" . WCF_N . "_member
                WHERE       userID = ?
                    AND     gameID = ?
                    AND     isDisabled = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $userID,
            RP_DEFAULT_GAME_ID,
            0,
        ]);
        $characters = $statement->fetchObjects(Character::class, 'characterID');

        $characterProfile = [];
        foreach ($characters as $character) {
            $characterProfile[$character->characterID] = new CharacterProfile($character);
        }

        return $characterProfile;
    }

    /**
     * Returns the absolute location of the icon file.
     *
     * @return string[]
     */
    public function getAvatarFileUploadFileLocations(): ?array
    {
        if ($this->avatarID) {
            $avatar = new CharacterAvatar($this->avatarID);
            return [$avatar->getLocation()];
        }

        return null;
    }

    /**
     * Returns the character with the given character name.
     */
    public static function getCharacterByCharactername(string $name): Character
    {
        $sql = "SELECT      *
                FROM        rp" . WCF_N . "_member
                WHERE       characterName = ?
                    AND     gameID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $name,
            RP_DEFAULT_GAME_ID,
        ]);
        $row = $statement->fetchArray();
        if (!$row) $row = [];

        return new Character(null, $row);
    }

    /**
     * Returns the character's formatted notes.
     */
    public function getFormattedNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->enableUgc = false;
        $processor->process(
            $this->notes,
            'info.daries.rp.character.notes',
            $this->characterID,
            false
        );

        return $processor->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Character', [
                'application' => 'rp',
                'forceFrontend' => true,
                'object' => $this
        ]);
    }

    /**
     * Returns other characters of the current character owner.
     */
    public function getOtherCharacters(): array
    {
        if ($this->otherCharacters === null) {
            $list = new CharacterProfileList();
            $list->getConditionBuilder()->add('userID = ?', [$this->userID]);
            $list->getConditionBuilder()->add('characterID != ?', [$this->characterID]);
            $list->readObjects();
            $this->otherCharacters = $list->getObjects();
        }

        return $this->otherCharacters;
    }

    /**
     * @inheritDoc
     */
    public function getPopoverLinkClass(): string
    {
        return 'rpCharacterLink';
    }

    /**
     * Returns primary character.
     */
    public function getPrimaryCharacter(): ?CharacterProfile
    {
        if ($this->isPrimary || !$this->userID) {
            return new CharacterProfile($this);
        } else {
            $characterPrimaryIDs = UserStorageHandler::getInstance()->getField('characterPrimaryIDs', $this->userID);

            // cache does not exist or is outdated
            if ($characterPrimaryIDs === null) {
                $sql = "SELECT  gameID, characterID
                        FROM    rp" . WCF_N . "_member
                        WHERE   userID = ?
                            AND isPrimary = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->userID, 1]);
                $characterPrimaryIDs = $statement->fetchMap('gameID', 'characterID');

                // update storage characterPrimaryIDs
                UserStorageHandler::getInstance()->update(
                    $this->userID,
                    'characterPrimaryIDs',
                    \serialize($characterPrimaryIDs)
                );
            } else {
                $characterPrimaryIDs = \unserialize($characterPrimaryIDs);
            }

            return CharacterProfileRuntimeCache::getInstance()->getObject($characterPrimaryIDs[$this->gameID]);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->characterName;
    }

    /**
     * Returns user profile object.
     */
    public function getUserProfile(): UserProfile
    {
        if ($this->user === null) {
            $this->user = new UserProfile(new User($this->userID));
        }

        return $this->user;
    }

    /**
     * @inheritDoc
     */
    protected function handleData($data): void
    {
        parent::handleData($data);

        // handle condition data
        if (isset($data['additionalData'])) {
            $this->data['additionalData'] = @\unserialize($data['additionalData'] ?: '');

            if (!\is_array($this->data['additionalData'])) {
                $this->data['additionalData'] = [];
            }
        } else {
            $this->data['additionalData'] = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function __get($name): mixed
    {
        $value = parent::__get($name);

        if ($value === null && isset($this->data['additionalData'][$name])) {
            $value = $this->data['additionalData'][$name];
        }

        return $value;
    }

    /**
     * Returns character name.
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
