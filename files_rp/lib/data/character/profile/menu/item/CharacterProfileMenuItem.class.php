<?php

namespace rp\data\character\profile\menu\item;

use rp\system\menu\character\profile\content\ICharacterProfileMenuContent;
use wcf\data\DatabaseObject;
use wcf\data\TDatabaseObjectOptions;
use wcf\data\TDatabaseObjectPermissions;
use wcf\system\exception\ImplementationException;
use wcf\system\exception\ParentClassException;
use wcf\system\exception\SystemException;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

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
 * Represents a character profile menu item.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Profile\Menu\Item
 *
 * @property-read   int     $menuItemID     unique id of the character profile menu item
 * @property-read   int     $packageID      id of the package which delivers the character profile menu item
 * @property-read   string  $menuItem       textual identifier of the character profile menu item
 * @property-read   int     $showOrder      position of the character profile menu item in relation to its siblings
 * @property-read   string  $permissions    comma separated list of user group permissions of which the active user needs to have at least one to see the character profile menu item
 * @property-read   string  $options        comma separated list of options of which at least one needs to be enabled for the character profile menu item to be shown
 * @property-read   string  $className      name of the PHP class implementing `rp\system\menu\character\profile\content\ICharacterProfileMenuContent` handling outputting the content of the character profile tab
 */
class CharacterProfileMenuItem extends DatabaseObject
{
    use TDatabaseObjectOptions;
    use TDatabaseObjectPermissions;
    /**
     * content manager
     * @var ICharacterProfileMenuContent
     */
    protected $contentManager;

    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'member_profile_menu_item';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'menuItemID';

    /**
     * Returns the content manager for this menu item.
     *
     * @throws  SystemException
     */
    public function getContentManager(): ICharacterProfileMenuContent
    {
        if ($this->contentManager === null) {
            if (!\class_exists($this->className)) {
                throw new SystemException("Unable to find class '" . $this->className . "'");
            }

            if (!\is_subclass_of($this->className, SingletonFactory::class)) {
                throw new ParentClassException($this->className, SingletonFactory::class);
            }

            if (!\is_subclass_of($this->className, ICharacterProfileMenuContent::class)) {
                throw new ImplementationException($this->className, ICharacterProfileMenuContent::class);
            }

            $this->contentManager = \call_user_func([$this->className, 'getInstance']);
        }

        return $this->contentManager;
    }

    /**
     * Returns the item identifier, dots are replaced by underscores.
     */
    public function getIdentifier(): string
    {
        return \str_replace('.', '_', $this->menuItem);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return WCF::getLanguage()->get('rp.character.profile.menu.' . $this->menuItem);
    }
}
