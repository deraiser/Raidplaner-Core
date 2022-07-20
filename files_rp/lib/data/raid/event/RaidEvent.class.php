<?php

namespace rp\data\raid\event;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\system\request\LinkHandler;
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
 * Represents a raid event.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Event
 * 
 * @property-read   int         $eventID            unique id of the raid event
 * @property-read   string      $eventName          name of the raid event
 * @property-read   int|null    $pointAccountID     id of the point account, or `null` if not assigned
 * @property-read   int         $gameID             id of the game
 * @property-read   float       $defaultPoints      default points of the raid event
 * @property-read   string      $icon               icon of the raid event
 * @property-read   int         $showProfile        is `1` if the raid event is show in profile, otherwise `0`
 */
class RaidEvent extends DatabaseObject implements ITitledLinkObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'eventID';

    /**
     * point account object
     */
    protected ?PointAccount $pointAccount = null;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size): string
    {
        if (empty($this->icon)) return WCF::getPath('rp') . 'images/raid/event/unknown.png';
        if ($size === null) $size = $this->size;

        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="raidEventIcon">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return WCF::getPath('rp') . 'images/raid/event/' . $this->icon . '.png';
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink(
                'RaidList',
                [
                    'application' => 'rp',
                    'forceFrontend' => true
                ],
                'raidEventID=' . $this->eventID
        );
    }

    /**
     * Returns the point account with the given point account id or `null` if no such point account exists.
     */
    public function getPointAccount(): ?PointAccount
    {
        if ($this->pointAccount === null) {
            $this->pointAccount = PointAccountCache::getInstance()->getPointAccountByID($this->pointAccountID);
        }

        return $this->pointAccount;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->eventName);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
