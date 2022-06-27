<?php

namespace rp\system\menu\character\profile;

use rp\data\character\profile\menu\item\CharacterProfileMenuItem;
use rp\system\cache\builder\CharacterProfileMenuCacheBuilder;
use wcf\system\event\EventHandler;
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
 * Builds the character profile menu.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile
 */
class CharacterProfileMenu extends SingletonFactory
{
    /**
     * active menu item
     */
    public ?CharacterProfileMenuItem $activeMenuItem = null;

    /**
     * list of all menu items
     * @var CharacterProfileMenuItem[]
     */
    public array $menuItems;

    /**
     * Checks the options and permissions of given menu item.
     */
    protected function checkMenuItem(CharacterProfileMenuItem $item): bool
    {
        return $item->validateOptions() && $item->validatePermissions();
    }

    /**
     * Checks the options and permissions of the menu items.
     */
    protected function checkMenuItems(): void
    {
        foreach ($this->menuItems as $key => $item) {
            if (!$this->checkMenuItem($item)) {
                // remove this item
                unset($this->menuItems[$key]);
            }
        }
    }

    /**
     * Returns the first visible menu item.
     */
    public function getActiveMenuItem(int $characterID = 0): ?CharacterProfileMenuItem
    {
        if (empty($this->menuItems)) {
            return null;
        }

        if ($this->activeMenuItem === null) {
            if (!empty($characterID)) {
                foreach ($this->menuItems as $menuItem) {
                    if ($menuItem->getContentManager()->isVisible($characterID)) {
                        $this->activeMenuItem = $menuItem;
                        break;
                    }
                }
            } else {
                $this->activeMenuItem = \reset($this->menuItems);
            }
        }

        return $this->activeMenuItem;
    }

    /**
     * Returns a specific menu item.
     */
    public function getMenuItem(string $menuItem): ?CharacterProfileMenuItem
    {
        foreach ($this->menuItems as $item) {
            if ($item->menuItem == $menuItem) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Returns the list of menu items.
     *
     * @return  CharacterProfileMenuItem[]
     */
    public function getMenuItems(): array
    {
        return $this->menuItems;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        // get menu items from cache
        $this->loadCache();

        // check menu items
        $this->checkMenuItems();

        // call init event
        EventHandler::getInstance()->fireAction($this, 'init');
    }

    /**
     * Loads cached menu items.
     */
    protected function loadCache(): void
    {
        // call loadCache event
        EventHandler::getInstance()->fireAction($this, 'loadCache');

        $this->menuItems = CharacterProfileMenuCacheBuilder::getInstance()->getData();
    }

    /**
     * Sets active menu item.
     */
    public function setActiveMenuItem(string $menuItem): bool
    {
        foreach ($this->menuItems as $item) {
            if ($item->menuItem == $menuItem) {
                $this->activeMenuItem = $item;

                return true;
            }
        }

        return false;
    }
}
