<?php

namespace rp\data\character\profile\menu\item;

use rp\system\cache\builder\CharacterProfileMenuCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
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
 * Provides functions to edit character profile menu items.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character\Profile\Menu\Item
 *
 * @method      CharacterProfileMenuItem    getDecoratedObject()
 * @mixin       CharacterProfileMenuItem
 */
class CharacterProfileMenuItemEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = CharacterProfileMenuItem::class;

    /**
     * @inheritDoc
     */
    public static function create(array $parameters = []): CharacterProfileMenuItem
    {
        // calculate show order
        $parameters['showOrder'] = self::getShowOrder($parameters['showOrder'] ?? 0);

        return parent::create($parameters);
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        // update show order
        $sql = "UPDATE  rp" . WCF_N . "_member_profile_menu_item
                SET     showOrder = showOrder - 1
                WHERE   showOrder >= ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->showOrder]);

        parent::delete();
    }

    /**
     * Returns show order for a new menu item.
     */
    protected static function getShowOrder(int $showOrder = 0): int
    {
        if ($showOrder == 0) {
            // get next number in row
            $sql = "SELECT  MAX(showOrder) AS showOrder
                    FROM    rp" . WCF_N . "_member_profile_menu_item";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute();
            $row = $statement->fetchArray();
            if (!empty($row)) {
                $showOrder = \intval($row['showOrder']) + 1;
            } else {
                $showOrder = 1;
            }
        } else {
            $sql = "UPDATE  rp" . WCF_N . "_member_profile_menu_item
                    SET     showOrder = showOrder + 1
                    WHERE   showOrder >= ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$showOrder]);
        }

        return $showOrder;
    }

    /**
     * @inheritDoc
     */
    public static function resetCache(): void
    {
        CharacterProfileMenuCacheBuilder::getInstance()->reset();
    }

    /**
     * @inheritDoc
     */
    public function update(array $parameters = []): void
    {
        if (isset($parameters['showOrder'])) {
            $this->updateShowOrder($parameters['showOrder']);
        }

        parent::update($parameters);
    }

    /**
     * Updates show order for current menu item.
     *
     * @param int $showOrder
     */
    protected function updateShowOrder(int $showOrder): void
    {
        if ($this->showOrder != $showOrder) {
            if ($showOrder < $this->showOrder) {
                $sql = "UPDATE  rp" . WCF_N . "_member_profile_menu_item
                        SET     showOrder = showOrder + 1
                        WHERE   showOrder >= ?
                            AND showOrder < ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $showOrder,
                    $this->showOrder,
                ]);
            } elseif ($showOrder > $this->showOrder) {
                $sql = "UPDATE  rp" . WCF_N . "_member_profile_menu_item
                        SET     showOrder = showOrder - 1
                        WHERE   showOrder <= ?
                            AND showOrder > ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $showOrder,
                    $this->showOrder,
                ]);
            }
        }
    }
}
