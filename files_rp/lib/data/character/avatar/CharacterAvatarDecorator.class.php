<?php

namespace rp\data\character\avatar;

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
 * Wraps avatars to provide compatibility layers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Avatar
 */
class CharacterAvatarDecorator implements ICharacterAvatar, ISafeFormatAvatar
{
    private ICharacterAvatar $avatar;

    public function __construct(ICharacterAvatar $avatar)
    {
        $this->avatar = $avatar;
    }

    /**
     * @inheritDoc
     */
    public function getHeight(): int
    {
        return $this->avatar->getHeight();
    }

    /**
     * @inheritDoc
     */
    public function getImageTag(?int $size = null, bool $lazyLoading = true): string
    {
        return $this->avatar->getImageTag($size, $lazyLoading);
    }

    /**
     * @inheritDoc
     */
    public function getSafeImageTag(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeImageTag($size);
        }

        return $this->avatar->getImageTag($size);
    }

    /**
     * @inheritDoc
     */
    public function getSafeURL(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    /**
     * @inheritDoc
     */
    public function getURL(?int $size = null): string
    {
        return $this->avatar->getURL();
    }

    /**
     * @inheritDoc
     */
    public function getWidth(): int
    {
        return $this->avatar->getWidth();
    }
}
