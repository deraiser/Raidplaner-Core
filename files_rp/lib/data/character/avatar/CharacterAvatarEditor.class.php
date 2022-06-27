<?php

namespace rp\data\character\avatar;

use wcf\data\DatabaseObjectEditor;
use wcf\system\image\ImageHandler;
use wcf\system\WCF;
use wcf\util\FileUtil;

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
 * Provides functions to edit avatars.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Avatar
 *
 * @method static   CharacterAvatar     create(array $parameters = [])
 * @method          CharacterAvatar     getDecoratedObject()
 * @mixin           CharacterAvatar
 */
class CharacterAvatarEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = CharacterAvatar::class;

    /**
     * Creates a WebP variant of the avatar, unless it is a GIF image. If the
     * character uploads a WebP image, this method will create a JPEG variant as a
     * fallback for ancient clients.
     *
     * Will return `true` if a variant has been created.
     */
    public function createAvatarVariant(): bool
    {
        if ($this->hasWebP) {
            return false;
        }

        if ($this->avatarExtension === "gif") {
            // We do not touch GIFs at all.
            return false;
        }

        $filename = $this->getLocation();
        $filenameWebP = $this->getLocation(null, true);

        $imageAdapter = ImageHandler::getInstance()->getAdapter();
        $imageAdapter->loadFile($filename);
        $image = $imageAdapter->getImage();

        $data = ["hasWebP" => 1];

        // If the uploaded avatar is already a WebP image, then create a JPEG
        // as a fallback image and flip the image data to match the JPEG.
        if ($this->avatarExtension === "webp") {
            $filenameJpeg = \preg_replace('~\.webp$~', '.jpeg', $filenameWebP);

            $imageAdapter->saveImageAs($image, $filenameJpeg, "jpeg", 80);

            // The new file has a different SHA1 hash, which means that apart from
            // updating the `fileHash` we also need to move it to a different physical
            // location.
            $newFileHash = \sha1_file($filenameJpeg);

            $tmpAvatar = clone $this;
            $tmpAvatar->data["avatarExtension"] = "jpeg";
            $tmpAvatar->data["fileHash"] = $newFileHash;
            $newLocation = $tmpAvatar->getLocation(null, false);

            $dir = \dirname($newLocation);
            if (!\file_exists($dir)) {
                FileUtil::makePath($dir);
            }

            \rename($filenameJpeg, $newLocation);

            $data = [
                "avatarExtension" => "jpeg",
                "fileHash" => $newFileHash,
            ];
        } else {
            $imageAdapter->saveImageAs($image, $this->getLocation(null, true), "webp", 80);
        }

        $this->update($data);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $sql = "DELETE FROM rp" . WCF_N . "_member_avatar
                WHERE       avatarID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->avatarID]);

        $this->deleteFiles();
    }

    /**
     * @inheritDoc
     */
    public static function deleteAll(array $objectIDs = []): int
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_member_avatar
                WHERE   avatarID IN (" . \str_repeat('?,', \count($objectIDs) - 1) . "?)";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($objectIDs);
        while ($avatar = $statement->fetchObject(self::$baseClass)) {
            $editor = new self($avatar);
            $editor->deleteFiles();
        }

        return parent::deleteAll($objectIDs);
    }

    /**
     * Deletes avatar files.
     */
    public function deleteFiles()
    {
        // delete original size
        @\unlink($this->getLocation(null, false));

        if ($this->hasWebP) {
            @\unlink($this->getLocation(null, true));
        }
    }
}
