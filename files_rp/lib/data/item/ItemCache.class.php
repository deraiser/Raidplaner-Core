<?php

namespace rp\data\item;

use rp\system\cache\builder\ItemCacheBuilder;
use wcf\system\SingletonFactory;

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
 * Manages the item cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Item
 */
class ItemCache extends SingletonFactory
{
    /**
     * cached item by item name
     * @var int[]
     */
    protected array $cachedItemNames = [];

    /**
     * cached items
     * @var Item[]
     */
    protected array $cachedItems = [];

    /**
     * Returns the item with the given item id or `null` if no such item exists.
     */
    public function getItemByID(int $itemID): ?Item
    {
        return $this->cachedItems[$itemID] ?? null;
    }

    /**
     * Returns the item with the given item name or `null` if no such item exists.
     */
    public function getItemByName(string $name): ?Item
    {
        $itemID = $this->cachedItemNames[\base64_encode($name)] ?? 0;
        return $this->getItemByID($itemID);
    }

    /**
     * Returns all items.
     * 
     * @return	Item[]
     */
    public function getItems(): array
    {
        return $this->cachedItems;
    }

    /**
     * Returns the items with the given item ids.
     * 
     * @return	Item[]
     */
    public function getItemsByIDs(array $itemIDs): array
    {
        $returnValues = [];

        foreach ($itemIDs as $itemID) {
            $returnValues[] = $this->getItemByID($itemID);
        }

        return $returnValues;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedItemNames = ItemCacheBuilder::getInstance()->getData([], 'itemNames');
        $this->cachedItems = ItemCacheBuilder::getInstance()->getData([], 'items');
    }
}
