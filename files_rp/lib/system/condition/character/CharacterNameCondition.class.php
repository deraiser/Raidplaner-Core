<?php

namespace rp\system\condition\character;

use rp\data\character\CharacterList;
use rp\system\condition\AbstractCondition;
use rp\system\form\builder\field\character\CharacterNameFormField;
use wcf\data\DatabaseObjectList;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\form\builder\field\IFormField;
use wcf\system\form\builder\IFormDocument;

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
 * Condition implementation for the character name of a character.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Condition\Character
 */
class CharacterNameCondition extends AbstractCondition
{

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, IFormDocument $form, array $conditionData = []): void
    {
        if (!($objectList instanceof CharacterList)) {
            throw new InvalidObjectArgument($objectList, CharacterList::class, 'Object list');
        }

        $value = $this->getValue($form);
        if ($value !== null && !empty($value)) {
            $objectList->getConditionBuilder()->add(
                'member.characterName LIKE ?',
                ['%' . \addcslashes($value, '_%') . '%']
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getFormField(): IFormField
    {
        return CharacterNameFormField::create($this->getID())
                ->label('rp.character.characterName')
                ->maximumLength(255);
    }

    /**
     * @inheritDoc
     */
    public function getID(): string
    {
        return 'characterName';
    }
}
