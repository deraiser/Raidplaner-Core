<?php

namespace rp\system\condition;

use wcf\data\DatabaseObjectList;
use wcf\data\IDatabaseObjectProcessor;
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
 * Every concrete condition implementation needs to implement this interface.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Condition
 */
interface ICondition extends IDatabaseObjectProcessor
{

    /**
     * Adds a condition to the given object list based on the given condition data.
     * 
     * @throws  \InvalidArgumentException   if the given object list object is no object of the expected database object list class
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, IFormDocument $form, array $conditionData = []): void;

    /**
     * Returns the output form field for setting up the condition.
     */
    public function getFormField(): IFormField;

    /**
     * Returns the id this form field for setting up the condition.
     */
    public function getID(): string;

    /**
     * Returns the data of the form node based on the form node id.
     */
    public function getValue(IFormDocument $form): mixed;

    /**
     * Set the data of the form node based on the form node id.
     */
    public function setValue(mixed $value, IFormDocument $form): void;
}
