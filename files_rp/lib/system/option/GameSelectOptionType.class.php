<?php

namespace rp\system\option;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\option\Option;
use wcf\system\exception\UserInputException;
use wcf\system\option\SelectOptionType;
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
 * Option type implementation for game select lists.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Option
 */
class GameSelectOptionType extends SelectOptionType
{

    /**
     * @inheritDoc
     */
    public function getFormElement(Option $option, $value): string
    {
        $options = $this->parseEnableOptions($option);

        $selectOptions = $this->parseSelectOptions();
        \uasort($selectOptions, [$this, 'sortByTitle']);

        WCF::getTPL()->assign([
            'disableOptions' => $options['disableOptions'],
            'enableOptions' => $options['enableOptions'],
            'option' => $option,
            'selectOptions' => $selectOptions,
            'value' => $value
        ]);
        return WCF::getTPL()->fetch('selectOptionType');
    }

    /**
     * @inheritDoc
     * @return  Game[]
     */
    public function parseSelectOptions(): array
    {
        return GameCache::getInstance()->getGames();
    }

    /**
     * Sorts results by title.
     */
    protected function sortByTitle($objectA, $objectB): mixed
    {
        return \strcmp($objectA->getTitle(), $objectB->getTitle());
    }

    /**
     * @inheritDoc
     */
    public function validate(Option $option, $newValue): void
    {
        if (!empty($newValue)) {
            $options = $this->parseSelectOptions();
            if (!isset($options[$newValue])) {
                throw new UserInputException($option->optionName, 'validationFailed');
            }
        }
    }
}
