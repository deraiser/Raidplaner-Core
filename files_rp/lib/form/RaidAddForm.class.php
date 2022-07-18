<?php

namespace rp\form;

use rp\data\character\CharacterList;
use rp\data\event\Event;
use rp\data\event\EventEditor;
use rp\data\point\account\PointAccountCache;
use rp\data\raid\event\RaidEventCache;
use rp\data\raid\RaidAction;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\form\builder\field\character\CharacterMultipleSelectionFormField;
use rp\system\form\builder\field\raid\RaidItemsFormField;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\MultipleSelectionFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

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
 * Shows the raid add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Form
 */
class RaidAddForm extends AbstractFormBuilderForm
{
    /**
     * event object
     */
    public ?Event $event = null;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['mod.rp.canAddRaid'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = RaidAction::class;

    /**
     * @inheritDoc
     */
    public function checkPermissions(): void
    {
        if ($this->formAction == 'create' && $this->event !== null) {
            if ($this->event->getController()->isLeader()) {
                $this->neededPermissions = [];
            } else {
                throw new PermissionDeniedException();
            }
        }

        parent::checkPermissions();
    }

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('raidTabMenu');
        $this->form->appendChild($tabMenu);

        // raid tab
        $raidTab = TabFormContainer::create('raidTab');
        $raidTab->label('rp.event.raid.title');
        $tabMenu->appendChild($raidTab);

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            DateFormField::create('date')
            ->label('rp.event.date')
            ->required()
            ->supportTime(),
            SingleSelectionFormField::create('raidEventID')
            ->label('rp.raid.event.title')
            ->required()
            ->options(function () {
                $options = [];
                $pointAccounts = PointAccountCache::getInstance()->getPointAccounts();
                $raidEvents = RaidEventCache::getInstance()->getRaidEvents();

                foreach ($pointAccounts as $pointAccount) {
                    $options[] = [
                        'depth' => 0,
                        'isSelectable' => false,
                        'label' => $pointAccount->pointAccountName,
                        'value' => ''
                    ];

                    foreach ($raidEvents as $raidEvent) {
                        if ($raidEvent->pointAccountID === null || $raidEvent->pointAccountID != $pointAccount->pointAccountID) continue;

                        $options[] = [
                            'depth' => 1,
                            'label' => $raidEvent->eventName,
                            'value' => $raidEvent->eventID
                        ];

                        unset($raidEvents[$raidEvent->eventID]);
                    }
                }

                if (!empty($raidEvents)) {
                    foreach ($raidEvents as $raidEvent) {
                        $options[] = [
                            'depth' => 0,
                            'label' => $raidEvent->eventName,
                            'value' => $raidEvent->eventID
                        ];
                    }
                }

                return $options;
            }, true),
            FloatFormField::create('points')
            ->label('rp.raid.event.points')
            ->available(RP_POINTS_ENABLED)
            ->minimum(0)
            ->value(0),
            TextFormField::create('notes')
            ->label('rp.event.notes')
            ->maximumLength(255),
        ]);
        $raidTab->appendChild($dataContainer);

        // item tab
        $itemsTab = TabFormContainer::create('itemsTab');
        $itemsTab->label('rp.raid.items');
        $tabMenu->appendChild($itemsTab);

        $itemsContainer = FormContainer::create('itemsContainer')
            ->appendChild(
            RaidItemsFormField::create()
            ->available(RP_ENABLE_ITEM)
        );
        $itemsTab->appendChild($itemsContainer);

        if ($this->formAction == 'create' && $this->event === null) {
            $charactersFormField = CharacterMultipleSelectionFormField::create('attendees')
                ->label('rp.event.raid.attendee.character')
                ->filterable()
                ->required()
                ->addValidator(new FormFieldValidator('empty', static function (MultipleSelectionFormField $formField) {
                        if (empty($formField->getSaveValue())) {
                            $formField->addValidationError(new FormFieldValidationError('empty'));
                        }
                    }));

            $parameters = [
                'charactersFormField' => $charactersFormField
            ];
            EventHandler::getInstance()->fireAction($this, 'attendeesCreateForm', $parameters);

            if (!isset($parameters['fieldChanged'])) {
                $charactersFormField->options(function () {
                    $characterList = new CharacterList();
                    $characterList->getConditionBuilder()->add('member.gameID = ?', [RP_DEFAULT_GAME_ID]);
                    $characterList->getConditionBuilder()->add('member.isDisabled = ?', [0]);
                    if (!empty($this->parameters['characterIDs'])) {
                        $characterList->getConditionBuilder()->add('member.characterID NOT IN (?)', [$this->parameters['characterIDs']]);
                    }
                    $characterList->readObjects();

                    $options = [];
                    foreach ($characterList->getObjects() as $character) {
                        $options[] = [
                            'depth' => 0,
                            'label' => $character->getTitle(),
                            'userID' => $character->userID,
                            'value' => $character->charcterID,
                        ];
                    }
                    return $options;
                }, false, false);
            }
            $dataContainer->appendChild($charactersFormField);
        }
    }

    /**
     * @inheritDoc
     */
    public function readData()
    {
        parent::readData();

        if (empty($_POST) && $this->formAction == 'create' && $this->event !== null) {
            $dateFormField = $this->form->getNodeById('date');
            $dateFormField->value($this->event->startTime);

            $notesFormField = $this->form->getNodeById('notes');
            $notesFormField->value($this->event->getFormattedPlainNotes());

            $raidEventFormField = $this->form->getNodeById('raidEventID');
            $raidEventFormField->value($this->event->raidEventID);

            $pointsFormField = $this->form->getNodeById('points');
            $pointsFormField->value($this->event->points);
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['eventID'])) {
            $this->event = EventRuntimeCache::getInstance()->getObject($_REQUEST['eventID']);

            if (!$this->event->isRaidEvent()) {
                throw new IllegalLinkException();
            }
        }
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
        } elseif ($this->formAction === 'edit') {
            $action = 'update';
        }

        $formData = $this->form->getData();
        if (!isset($formData['data'])) {
            $formData['data'] = [];
        }
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);
        $formData['event'] = $this->event;

        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            $formData
        );
        $this->objectAction->executeAction();

        $this->saved();

        WCF::getTPL()->assign('success', true);

        if ($this->formAction === 'create' && $this->objectEditLinkController) {
            WCF::getTPL()->assign(
                'objectEditLink',
                LinkHandler::getInstance()->getControllerLink($this->objectEditLinkController, [
                    'application' => $this->objectEditLinkApplication,
                    'id' => $this->objectAction->getReturnValues()['returnValues']->getObjectID(),
                ])
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function saved(): void
    {
        if ($this->event !== null) {
            $eventEditor = new EventEditor($this->event);
            $eventEditor->update([
                'raidID' => $this->objectAction->getReturnValues()['returnValues']->getObjectID(),
            ]);
        }

        AbstractForm::saved();

        HeaderUtil::redirect(LinkHandler::getInstance()->getLink('RaidList', ['application' => 'rp']));
        exit;
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        $parameters = [];

        if ($this->formAction == 'create' && $this->event !== null) {
            $parameters['eventID'] = $this->event->eventID;
        }

        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
            } else {
                $object = $this->formObject;

                $parameters['id'] = $object->{$object::getDatabaseTableIndexName()};
            }
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
