<?php

namespace rp\acp\form;

use rp\data\character\Character;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\IFormNode;

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
 * Shows the character edit form.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class CharacterEditForm extends CharacterAddForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.list';

    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Character($_REQUEST['id']);
            if (!$this->formObject->characterID) {
                throw new IllegalLinkException();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function setFormObjectData(): void
    {
        parent::setFormObjectData();

        if (empty($_POST)) {
            foreach ($this->formObject->additionalData as $key => $value) {
                /** @var IFormNode $node */
                $node = $this->form->getNodeById($key);
                if ($node !== null) {
                    $node->value($value);
                }
            }
        }
    }
}
