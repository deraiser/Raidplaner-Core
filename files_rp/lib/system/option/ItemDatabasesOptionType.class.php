<?php

namespace rp\system\option;

use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\AbstractOptionType;
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
 * Option type implementation for item database selection.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Option
 */
class ItemDatabasesOptionType extends AbstractOptionType
{
    /**
     * list of available item databases
     */
    protected static ?array $databases = null;

    /**
     * @inheritDoc
     */
    public function getData(Option $option, $newValue): string
    {
        if (!\is_array($newValue)) {
            return '';
        }

        return \implode(',', $newValue);
    }

    /**
     * Returns the list of available item databases.
     */
    protected static function getDatabases(): array
    {
        if (self::$databases === null) {
            self::$databases = [];
            $sql = "SELECT  databaseName
                    FROM    rp" . WCF_N . "_item_database";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute();
            self::$databases = $statement->fetchAll(\PDO::FETCH_COLUMN);
        }

        return self::$databases;
    }

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value): string
    {
        $databases = self::getDatabases();
        if ($option->issortable && $value) {
            $sortedDatabases = \explode(',', $value);

            // remove old databases
            $sortedDatabases = \array_intersect($sortedDatabases, $databases);

            // append the non-checked databases after the checked and sorted databases
            $databases = \array_merge($sortedDatabases, \array_diff($databases, $sortedDatabases));
        }

        WCF::getTPL()->assign([
            'option' => $option,
            'value' => \explode(',', $value),
            'availableDatabases' => $databases,
        ]);

        return WCF::getTPL()->fetch('itemDatabaseType', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue): void
    {
        if (!\is_array($newValue)) {
            $newValue = [];
        }

        foreach ($newValue as $databaseName) {
            if (!\in_array($databaseName, self::getDatabases())) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }
}
