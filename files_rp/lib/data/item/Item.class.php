<?php

namespace rp\data\item;

use wcf\data\DatabaseObject;
use wcf\data\ILinkableObject;
use wcf\data\IPopoverObject;
use wcf\system\request\IRouteController;
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
 * Represents a item.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Item
 *
 * @property-read   int         $itemID             unique id of the item
 * @property-read   string      $itemName           unique item name of the item
 * @property-read   int         $date               date of the item created
 * @property-read   string      $searchItemID       id of the item from the database
 * @property-read   array       $additionalData     array with additional data of the item
 */
class Item extends DatabaseObject implements IPopoverObject, IRouteController, ILinkableObject
{
    /**
     * item icon expire time (days)
     * @var int
     */
    const ITEM_ICON_CACHE_EXPIRE = 7;

    /**
     * item icon local cache location
     * @var string
     */
    const ITEM_ICON_CACHE_LOCATION = 'images/item/icons/%s.%s';

    /**
     * urls of this item icon
     */
    protected string $url = '';

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size = null): string
    {
        return '<img src="' . StringUtil::encodeHTML($this->getIconPath()) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="itemIcon">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        if (!$this->icon) $this->url = WCF::getPath() . 'images/placeholderTiny.png';

        if (empty($this->url)) {
            // try to use cached item icon
            $cachedFilename = \sprintf(
                self::ITEM_ICON_CACHE_LOCATION,
                \md5(\mb_strtolower($this->icon)),
                ($this->itemIconFileExtension ?: $this->iconExtension)
            );

            if (\file_exists(RP_DIR . $cachedFilename) && \filemtime(RP_DIR . $cachedFilename) > (TIME_NOW - (self::ITEM_ICON_CACHE_EXPIRE * 86400))) {
                $this->url = WCF::getPath('rp') . $cachedFilename;
            } else {
                $this->url = LinkHandler::getInstance()->getLink('ItemIconDownload', [
                    'application' => 'rp',
                    'forceFrontend' => true,
                    ], 'itemID=' . $this->itemID);
            }
        }

        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Item', [
                'application' => 'rp',
                'forceFrontend' => true,
                'object' => $this,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getPopoverLinkClass(): string
    {
        return 'rpItemLink';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->itemName;
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
}
