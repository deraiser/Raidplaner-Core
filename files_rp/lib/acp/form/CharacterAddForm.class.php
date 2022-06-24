<?php

namespace rp\acp\form;

use rp\data\character\CharacterAction;
use rp\data\character\CharacterProfile;
use rp\data\game\GameCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\user\UserFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\request\RequestHandler;
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
 * Shows the character add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class CharacterAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.add';

    /**
     * ids of the fields containing object data
     * @var string[]
     */
    public array $characterFields = [
        'characterName',
        'guildName',
        'notes',
        'userID'
    ];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = CharacterAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterTabMenu');
        $this->form->appendChild($tabMenu);

        // data tab
        $dataTab = TabFormContainer::create('dataTab');
        $dataTab->label('wcf.global.form.data');
        $tabMenu->appendChild($dataTab);

        $dataContainer = FormContainer::create('data')
            ->appendChildren([
            TextFormField::create('characterName')
            ->label('wcf.global.name')
            ->required()
            ->autoFocus()
            ->maximumLength(100)
            ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                        $value = $formField->getSaveValue();

                        if ($this->formAction === 'create' || ($value !== $this->formObject->characterName)) {
                            $character = CharacterProfile::getCharacterProfilByCharactername($value);
                            if ($character->characterID) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'exists',
                                        'rp.character.name.error.exists',
                                        ['character' => $character]
                                    )
                                );
                            }
                        }
                    })),
            WysiwygFormField::create('notes')
            ->label('rp.character.notes')
            ->objectType('info.daries.rp.character.notes'),
        ]);
        $dataTab->appendChild($dataContainer);

        if (RequestHandler::getInstance()->isACPRequest()) {
            $this->userFormField($dataContainer);
        }

        // character tab
        $characterTab = TabFormContainer::create('characterTab');
        $characterTab->label('rp.character.category.character');
        $tabMenu->appendChild($characterTab);

        $characterContainer = FormContainer::create('character');
        $characterTab->appendChild($characterContainer);

        EventHandler::getInstance()->fireAction(
            $this,
            'createForm',
            [
                'form' => $this->form,
            ],
        );
    }

    /**
     * @inheritDoc
     */
    public function save(): void
    {
        AbstractForm::save();

        $action = $this->formAction;
        if ($this->objectActionName) {
            $action = $this->objectActionName;
        } else if ($this->formAction === 'edit') {
            $action = 'update';
        }
        $formData = $this->form->getData();
        if (!isset($formData['data'])) $formData['data'] = [];
        $formData['data'] = \array_merge(
            $this->additionalFields,
            $formData['data'],
        );

        $characterData = [
            'gameID' => RP_DEFAULT_GAME_ID,
        ];
        foreach ($this->characterFields as $characterField) {
            if (isset($formData['data'][$characterField])) {
                $characterData[$characterField] = $formData['data'][$characterField];
                unset($formData['data'][$characterField]);
            }
        }

        if (!RequestHandler::getInstance()->isACPRequest()) {
            $characterData['userID'] = WCF::getUser()->userID;
        }

        if (!isset($characterData['userID']) || $characterData['userID'] === 0) {
            $characterData['userID'] = null;
        }

        $characterData['additionalData'] = @\serialize($formData['data']);
        unset($formData['data']);

        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            \array_merge(['data' => $characterData], $formData)
        );
        $this->objectAction->executeAction();

        $this->saved();

        WCF::getTPL()->assign('success', true);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        if (!RP_DEFAULT_GAME_ID) throw new IllegalLinkException();
        if (GameCache::getInstance()->getGameByID(RP_DEFAULT_GAME_ID) === null) throw new IllegalLinkException();

        parent::readParameters();
    }

    /**
     * Sets the UserFormField for the field `userID` into this FormContainer
     */
    protected function userFormField(FormContainer $dataContainer): void
    {
        $dataContainer->appendChild(
            UserFormField::create('userID')
                ->label('rp.acp.character.user')
        );
    }
}
