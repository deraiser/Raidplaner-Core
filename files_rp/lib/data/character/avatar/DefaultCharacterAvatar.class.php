<?php

namespace rp\data\character\avatar;

use wcf\system\WCF;
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
 * Represents a default avatar.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Avatar
 */
class DefaultCharacterAvatar implements ICharacterAvatar, ISafeFormatAvatar
{
    /**
     * image size
     */
    public int $size = CharacterAvatar::AVATAR_SIZE;

    /**
     * content of the `src` attribute
     */
    protected string $src = '';

    /**
     * DefaultAvatar constructor.
     */
    public function __construct(string $characterName = '')
    {
        if (\defined('AVATAR_DEFAULT_TYPE') && \AVATAR_DEFAULT_TYPE === 'initials' && !empty($characterName)) {
            $words = \explode(' ', $characterName);
            $count = \count($words);
            if ($count > 1) {
                // combine the first character of each the first and the last word
                $text = \mb_strtoupper(\mb_substr($words[0], 0, 1) . \mb_substr($words[$count - 1], 0, 1));
            } else {
                // use the first two characters
                $text = \mb_strtoupper(\mb_substr($characterName, 0, 2));
            }

            $text = \htmlspecialchars($text, \ENT_XML1, 'UTF-8');

            $backgroundColor = \substr(\sha1($characterName), 0, 6);

            $perceptiveLuminance = $this->getPerceptiveLuminance(
                \hexdec($backgroundColor[0] . $backgroundColor[1]),
                \hexdec($backgroundColor[2] . $backgroundColor[3]),
                \hexdec($backgroundColor[4] . $backgroundColor[5])
            );

            $textColor = ($perceptiveLuminance < 0.3) ? '000' : 'fff';

            // the <path> is basically a shorter version of a <rect>
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="128" height="128"><path fill="#{$backgroundColor}" d="M0 0h16v16H0z"/><text x="8" y="8" fill="#{$textColor}" text-anchor="middle" dy=".3em" font-family="Arial" font-size="7">{$text}</text></svg>
SVG;

            $this->src = "data:image/svg+xml;base64," . \base64_encode($svg);
        } else {
            $this->src = WCF::getPath('rp') . 'images/avatars/avatar-default.svg';
        }
    }

    /**
     * @inheritDoc
     */
    public function getHeight(): int
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function getImageTag(?int $size = null): string
    {
        if ($size === null) $size = $this->size;

        return '<img src="' . StringUtil::encodeHTML($this->getURL($size)) . '" width="' . $size . '" height="' . $size . '" alt="" class="characterAvatarImage">';
    }

    /**
     * Returns the perceived luminance of the given color.
     */
    protected function getPerceptiveLuminance(int $r, int $g, int $b): float
    {
        return 1 - (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
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
        return WCF::getPath('rp') . 'images/avatars/avatar-default.svg';
    }

    /**
     * @inheritDoc
     */
    public function getURL(?int $size = null): string
    {
        return $this->src;
    }

    /**
     * @inheritDoc
     */
    public function getWidth(): int
    {
        return $this->size;
    }
}
