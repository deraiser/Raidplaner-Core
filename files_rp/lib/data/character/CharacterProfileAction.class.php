<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarAction;
use rp\data\character\avatar\CharacterAvatarEditor;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\IPopoverAction;
use wcf\system\exception\UserInputException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;
use wcf\util\ImageUtil;

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
 * Executes character profile-related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 */
class CharacterProfileAction extends CharacterAction implements IPopoverAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getPopover'];

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $characterID = \reset($this->objectIDs);

        if ($characterID) {
            $characterProfile = CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
            if ($characterProfile) {
                WCF::getTPL()->assign('character', $characterProfile);
            } else {
                WCF::getTPL()->assign('unknownCharacter', true);
            }
        } else {
            WCF::getTPL()->assign('unknownCharacter', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('characterProfilePreview', 'rp'),
        ];
    }

    /**
     * Sets an avatar for a given character. The given file will be renamed and is gone after this method call.
     *
     * @throws UserInputException If none or more than one character is given.
     * @throws \InvalidArgumentException If the given file is not an image or is incorrectly sized.
     */
    public function setAvatar(): array
    {
        $character = $this->getSingleObject();

        $imageData = \getimagesize($this->parameters['fileLocation']);

        if (!$imageData) {
            throw new \InvalidArgumentException("The given file is not an image.");
        }

        if ($imageData[0] != CharacterAvatar::AVATAR_SIZE || $imageData[1] != CharacterAvatar::AVATAR_SIZE) {
            throw new \InvalidArgumentException(
                    \sprintf(
                        "The given file does not have the size of %dx%d",
                        CharacterAvatar::AVATAR_SIZE,
                        CharacterAvatar::AVATAR_SIZE
                    )
            );
        }

        $data = [
            'avatarName' => $this->parameters['filename'] ?? \basename($this->parameters['fileLocation']),
            'avatarExtension' => ImageUtil::getExtensionByMimeType($imageData['mime']),
            'width' => $imageData[0],
            'height' => $imageData[1],
            'characterID' => $character->characterID,
            'fileHash' => \sha1_file($this->parameters['fileLocation']),
        ];

        // create avatar
        $avatar = CharacterAvatarEditor::create($data);

        try {
            // check avatar directory
            // and create subdirectory if necessary
            $dir = \dirname($avatar->getLocation(null, false));
            if (!\file_exists($dir)) {
                FileUtil::makePath($dir);
            }

            \rename($this->parameters['fileLocation'], $avatar->getLocation(null, false));

            // Create the WebP variant or the JPEG fallback of the avatar.
            $avatarEditor = new CharacterAvatarEditor($avatar);
            if ($avatarEditor->createAvatarVariant()) {
                $avatar = new CharacterAvatar($avatar->avatarID);
            }

            // update character
            $characterEditor = new CharacterEditor($character->getDecoratedObject());
            $characterEditor->update([
                'avatarID' => $avatar->avatarID,
            ]);
        } catch (\Exception $e) {
            $editor = new CharacterAvatarEditor($avatar);
            $editor->delete();

            throw $e;
        }

        // delete old avatar
        if ($character->avatarID) {
            (new CharacterAvatarAction([$character->avatarID], 'delete'))->executeAction();
        }

        if ($character->userID) {
            // reset user storage
            UserStorageHandler::getInstance()->reset([$character->userID], 'charactersAvatar');
        }

        return [
            'avatar' => $avatar,
        ];
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        WCF::getSession()->checkPermissions(['user.rp.canViewCharacterProfile']);

        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }
}
