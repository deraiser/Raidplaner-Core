<?php

namespace wcf\system\package\plugin;

use rp\data\item\database\ItemDatabaseEditor;
use rp\data\item\database\ItemDatabaseList;
use rp\system\item\database\IItemDatabase;
use wcf\system\devtools\pip\IDevtoolsPipEntryList;
use wcf\system\devtools\pip\IGuiPackageInstallationPlugin;
use wcf\system\devtools\pip\TXmlGuiPackageInstallationPlugin;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ClassNameFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
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
 * Installs, updates and deletes item tool tip database.
 *
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPItemDatabasePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IGuiPackageInstallationPlugin
{
    use TXmlGuiPackageInstallationPlugin;
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = ItemDatabaseEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'item_database';

    /**
     * @inheritDoc
     */
    public $tagName = 'database';

    /**
     * @inheritDoc
     * @since   5.2
     */
    protected function addFormFields(IFormDocument $form): void
    {
        /** @var FormContainer $dataContainer */
        $dataContainer = $form->getNodeById('data');

        $dataContainer->appendChildren([
                TextFormField::create('databaseName')
                ->objectProperty('name')
                ->label('rp.acp.item.databaseName')
                ->description('rp.acp.item.databaseName.description')
                ->required()
                ->addValidator(new FormFieldValidator('format', static function (TextFormField $formField) {
                            if (\preg_match('~^[a-z][A-z]+$~', $formField->getValue()) !== 1) {
                                $formField->addValidationError(
                                    new FormFieldValidationError(
                                        'format',
                                        'rp.acp.item.databaseName.error.format'
                                    )
                                );
                            }
                        }))
                ->addValidator(new FormFieldValidator('uniqueness', function (TextFormField $formField) {
                            if (
                                $formField->getDocument()->getFormMode() === IFormDocument::FORM_MODE_CREATE || $this->editedEntry->getAttribute('name') !== $formField->getValue()
                            ) {
                                $databaseList = new ItemDatabaseList();
                                $databaseList->getConditionBuilder()->add('databaseName = ?', [$formField->getValue()]);

                                if ($databaseList->countObjects()) {
                                    $formField->addValidationError(
                                        new FormFieldValidationError(
                                            'format',
                                            'rp.acp.item.databaseName.error.notUnique'
                                        )
                                    );
                                }
                            }
                        })),
                ClassNameFormField::create()
                ->required()
                ->implementedInterface(IItemDatabase::class),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function fetchElementData(\DOMElement $element, $saveData): array
    {
        return [
            'className' => $element->nodeValue,
            'databaseName' => $element->getAttribute('name')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data)
    {
        $sql = "SELECT  *
                FROM    rp" . WCF_N . "_" . $this->tableName . "
                WHERE   databaseName = ?
                    AND packageID = ?";
        $parameters = [
            $data['databaseName'],
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
        return 'rpItemDatabase.xml';
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
                WHERE       databaseName = ?
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
        return [
            'className' => $data['nodeValue'],
            'databaseName' => $data['attributes']['name']
        ];
    }

    /**
     * @inheritDoc
     */
    protected function prepareXmlElement(\DOMDocument $document, IFormDocument $form)
    {
        $data = $form->getData()['data'];

        $database = $document->createElement($this->tagName, $data['className']);
        $database->setAttribute('name', $data['name']);

        return $database;
    }

    /**
     * @inheritDoc
     */
    protected function setEntryListKeys(IDevtoolsPipEntryList $entryList): void
    {
        $entryList->setKeys([
            'className' => 'wcf.form.field.className',
            'databaseName' => 'rp.acp.item.databaseName'
        ]);
    }
}
