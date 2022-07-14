<?php

namespace rp\system\form\builder\field\character;

use wcf\data\DatabaseObjectList;
use wcf\data\IObjectTreeNode;
use wcf\data\ITitledObject;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\WCF;
use wcf\util\ClassUtil;

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
 * Extended the Implementation of a form field for selecting multiple values for characters.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Form\Builder\Field\Character
 */
class CharacterMultipleSelectionFormField extends MultipleSelectionFormField
{
    /**
     * @inheritDoc
     */
    protected $templateApplication = 'rp';

    /**
     * @inheritDoc
     */
    protected $templateName = '__characterMultipleSelectionFormField';

    /**
     * @inheritDoc
     */
    public function options($options, $nestedOptions = false, $labelLanguageItems = true): self
    {
        parent::options($options, $nestedOptions, $labelLanguageItems);

        if ($nestedOptions) {
            foreach ($this->nestedOptions as $key => $option) {
                if (isset($options[$option['value']])) {
                    $this->nestedOptions[$key]['userID'] = $options[$option['value']]['userID'] ?? 0;
                }
            }
        }

        return $this;
    }
}
