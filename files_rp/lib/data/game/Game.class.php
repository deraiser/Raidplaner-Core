<?php

namespace rp\data\game;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
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
 * @author      Marco Daries
 * @package     Daries\RP\Data\Game
 * 
 * @property-read   int         $gameID             unique id of the game
 * @property-read   int         $packageID          id of the package which delivers the game
 * @property-read   string      $identifier         unique textual identifier of the game
 * @property-read   int         $maxLevel           max level of the game
 * @property-read   string      $icon               icon of the game
 */
class Game extends DatabaseObject implements ITitledObject
{

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size): string
    {
        if (empty($this->icon)) return '';
        if ($size === null) $size = 128;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->identifier . '/' . $this->icon . '.png';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get('rp.game.' . $this->identifier);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
