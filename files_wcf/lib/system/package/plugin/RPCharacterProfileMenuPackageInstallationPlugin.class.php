<?php

namespace wcf\system\package\plugin;

use rp\data\character\profile\menu\item\CharacterProfileMenuItem;
use rp\data\character\profile\menu\item\CharacterProfileMenuItemEditor;
use rp\data\character\profile\menu\item\CharacterProfileMenuItemList;
use rp\system\menu\character\profile\content\ICharacterProfileMenuContent;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ClassNameFormField;
use wcf\system\form\builder\field\IntegerFormField;
use wcf\system\form\builder\field\option\OptionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\user\group\option\UserGroupOptionFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;
use wcf\util\StringUtil;

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
 * Installs, updates and deletes character profile menu items.
 *
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPCharacterProfileMenuPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IGuiPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;
    /**
     * table application prefix
     * @var string
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = CharacterProfileMenuItemEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'member_profile_menu_item';

    /**
     * @inheritDoc
     */
    public $tagName = 'characterprofilemenuitem';

    /**
     * @inheritDoc
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
                TextFormField::create('menuItem')
                ->objectProperty('name')
                ->label('rp.acp.pip.characterProfileMenu.menuItem')
                ->description('rp.acp.pip.characterProfileMenu.menuItem.description')
                ->required()
                ->addValidator(new FormFieldValidator('format', static function (TextFormField $formField) {
                            if (!\preg_match('~^[a-z][A-z]+$~', $formField->getValue())) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'format',
                                        'rp.acp.pip.characterProfileMenu.menuItem.error.format'
                                    )
                                );
                            }
                        }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                            if (
                                $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE || $this->editedEntry->getAttribute('name') !== $formField->getValue()
                            ) {
                                $menuItemList = new CharacterProfileMenuItemList();
                                $menuItemList->getConditionBuilder()->add(
                                    'member_profile_menu_item.menuItem = ?',
                                    [$formField->getValue()]
                                );

                                if ($menuItemList->countObjects() > 0) {
                                    $formField->addValidationError(
                                        new FormFieldValidationError(
                                            'notUnique',
                                            'rp.acp.pip.characterProfileMenu.menuItem.error.notUnique'
                                        )
                                    );
                                }
                            }
                        })),
                ClassNameFormField::create()
                ->objectProperty('classname')
                ->required()
                ->implementedInterface(ICharacterProfileMenuContent::class),
                IntegerFormField::create('showOrder')
                ->objectProperty('showorder')
                ->label('wcf.form.field.showOrder')
                ->description('rp.acp.pip.characterProfileMenu.showOrder.description')
                ->nullable()
                ->minimum(1),
                OptionFormField::create()
                ->description('wcf.acp.pip.abstractMenu.options.description')
                ->packageIDs(\array_merge(
                        [$this->installation->getPackage()->packageID],
                        \array_keys($this->installation->getPackage()->getAllRequiredPackages())
                    )),
                UserGroupOptionFormField::create()
                ->description('wcf.acp.pip.abstractMenu.options.description')
                ->packageIDs(\array_merge(
                        [$this->installation->getPackage()->packageID],
                        \array_keys($this->installation->getPackage()->getAllRequiredPackages())
                    ))
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function fetchElementData(\DOMElement $element, $saveData): array
    {
        $data = [
            'className' => $element->getElementsByTagName('classname')->item(0)->nodeValue,
            'menuItem' => $element->getAttribute('name'),
            'packageID' => $this->installation->getPackage()->packageID,
        ];

        $options = $element->getElementsByTagName('options')->item(0);
        if ($options) {
            $data['options'] = StringUtil::normalizeCsv($options->nodeValue);
        } elseif ($saveData) {
            $data['options'] = '';
        }

        $permissions = $element->getElementsByTagName('permissions')->item(0);
        if ($permissions) {
            $data['permissions'] = StringUtil::normalizeCsv($permissions->nodeValue);
        } elseif ($saveData) {
            $data['permissions'] = '';
        }

        $showOrder = $element->getElementsByTagName('showorder')->item(0);
        if ($showOrder) {
            $data['showOrder'] = \intval($showOrder->nodeValue);
        }
        if ($saveData && $this->editedEntry === null) {
            // only set explicit showOrder when adding new menu item
            $data['showOrder'] = $this->getShowOrder($data['showOrder'] ?? null);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_" . $this->tableName . "
                WHERE   menuItem = ?
                    AND packageID = ?";
        $parameters = [
            $data['menuItem'],
            $this->installation->getPackageID(),
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @see IPackageInstallationPlugin::getDefaultFilename()
     */
    public static function getDefaultFilename(): string
    {
        return 'rpCharacterProfileMenu.xml';
    }

    /**
     * @inheritDoc
     */
    public function getElementIdentifier(\DOMElement $element): string
    {
        return $element->getAttribute('name');
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM rp" . WCF_N . "_" . $this->tableName . "
                WHERE       menuItem = ?
                        AND packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['name'],
                $this->installation->getPackageID(),
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        // adjust show order
        $showOrder = $data['elements']['showorder'] ?? null;
        $showOrder = $this->getShowOrder($showOrder);

        // merge values and default values
        return [
            'className' => $data['elements']['classname'],
            'menuItem' => $data['attributes']['name'],
            'options' => isset($data['elements']['options']) ? StringUtil::normalizeCsv($data['elements']['options']) : '',
            'permissions' => isset($data['elements']['permissions']) ? StringUtil::normalizeCsv($data['elements']['permissions']) : '',
            'showOrder' => $showOrder,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form): CharacterProfileMenuItem
    {
        $data = $form->getData()['data'];

        $characterProfileMenuItem = $document->createElement($this->tagName);
        $characterProfileMenuItem->setAttribute('name', $data['name']);

        $this->appendElementChildren(
            $characterProfileMenuItem,
            [
                'classname',
                'options' => '',
                'permissions' => '',
                'showorder' => null,
            ],
            $form
        );

        return $characterProfileMenuItem;
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'className' => 'wcf.form.field.className',
            'menuItem' => 'rp.acp.pip.characterProfileMenu.menuItem',
        ]);
    }
}
