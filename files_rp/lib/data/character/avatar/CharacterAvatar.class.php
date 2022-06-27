<?php

namespace rp\data\character\avatar;

use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\util\ImageUtil;
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
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Avatar
 * 
 * @property-read   int         $avatarID           unique id of the character avatar
 * @property-read   string      $avatarName         name of the original avatar file
 * @property-read   string      $avatarExtension    extension of the avatar file
 * @property-read   int         $width              width of the character avatar image
 * @property-read   int         $height             height of the character avatar image
 * @property-read   int|null    $characterID        id of the character to which the character avatar belongs or null
 * @property-read   string      $fileHash           SHA1 hash of the original avatar file
 * @property-read   int         $hasWebP            `1` if there is a WebP variant, else `0`
 */
class CharacterAvatar extends DatabaseObject implements ICharacterAvatar, ISafeFormatAvatar
{
    /**
     * minimum height and width of an uploaded avatar
     * @var int
     */
    const AVATAR_SIZE = 128;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'member_avatar';

    /**
     * Returns the file name of this avatar.
     */
    public function getFilename(?int $size = null, ?bool $forceWebP = null): string
    {
        if ($forceWebP === true || ($forceWebP === null && $this->hasWebP && ImageUtil::browserSupportsWebP())) {
            $fileExtension = "webp";
        } else {
            $fileExtension = $this->avatarExtension;
        }

        $directory = \substr($this->fileHash, 0, 2);

        return \sprintf(
            '%s/%d-%s.%s',
            $directory,
            $this->avatarID,
            $this->fileHash . ($size !== null ? ('-' . $size) : ''),
            $fileExtension
        );
    }
    /**
     * @inheritDoc
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @inheritDoc
     */
    public function getImageTag(?int $size = null, bool $lazyLoading = true): string
    {
        return \sprintf(
            '<img src="%s" width="%d" height="%d" alt="" class="characterAvatarImage" loading="%s">',
            StringUtil::encodeHTML($this->getURL($size)),
            $size,
            $size,
            $lazyLoading ? 'lazy' : 'eager'
        );
    }

    /**
     * Returns the physical location of this avatar.
     */
    public function getLocation(?int $size = null, ?bool $forceWebP = null): string
    {
        return RP_DIR . 'images/avatars/' . $this->getFilename($size, $forceWebP);
    }

    /**
     * @inheritDoc
     */
    public function getSafeImageTag(?int $size = null): string
    {
        return '<img src="' . StringUtil::encodeHTML($this->getSafeURL($size)) . '" width="' . $size . '" height="' . $size . '" alt="" class="characterAvatarImage">';
    }

    /**
     * @inheritDoc
     */
    public function getSafeURL(?int $size = null): string
    {
        return WCF::getPath('rp') . 'images/avatars/' . $this->getFilename(null, false);
    }

    /**
     * @inheritDoc
     */
    public function getURL(?int $size = null): string
    {
        return WCF::getPath('rp') . 'images/avatars/' . $this->getFilename();
    }

    /**
     * @inheritDoc
     */
    public function getWidth(): int
    {
        return $this->width;
    }
}
