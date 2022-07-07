<?php

namespace rp\data\event\raid\attendee;

use rp\data\classification\ClassificationCache;
use rp\data\event\Event;
use rp\data\role\RoleCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\cache\runtime\EventRaidAttendeeRuntimeCache;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IPopoverAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\DialogFormDocument;
use wcf\system\form\builder\field\EmailFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\user\notification\UserNotificationHandler;
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
 * Executes event raid attendee related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event\Raid\Attendee
 * 
 * @method      EventRaidAttendeeEditor[]   getObjects()
 * @method      EventRaidAttendeeEditor     getSingleObject()
 */
class EventRaidAttendeeAction extends AbstractDatabaseObjectAction implements IPopoverAction
{
    /**
     * add dialog form document
     */
    protected ?DialogFormDocument $addDialog = null;

    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = [
        'createAddDialog',
        'getPopover',
        'submitAddDialong',
    ];

    protected ?EventRaidAttendee $attendee = null;

    /**
     * @inheritDoc
     */
    protected $className = EventRaidAttendeeEditor::class;

    /**
     * event object
     */
    protected ?Event $event = null;

    /**
     * @inheritDoc
     */
    public function create(): EventRaidAttendee
    {
        $this->parameters['data']['created'] = TIME_NOW;

        return parent::create();
    }

    public function createAddDialog(): array
    {
        return [
            'dialog' => $this->getAddDialog()->getHtml(),
            'formId' => 'addDialog'
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): array
    {
        // delete attendees
        parent::delete();

        foreach ($this->objects as $attendee) {
            if ($attendee->getCharacter()?->userID) {
                UserNotificationHandler::getInstance()->markAsConfirmed(
                    'status',
                    'info.daries.rp.raid.event.notification',
                    [$attendee->getCharacter()->userID],
                    [$attendee->getEvent()->eventID]
                );
            }
        }

        $this->unmarkItems();

        return [
            'objectIDs' => $this->objectIDs
        ];
    }

    /**
     * Creates a dialog and is returned.
     */
    protected function getAddDialog(): DialogFormDocument
    {
        if ($this->addDialog === null) {
            $this->addDialog = DialogFormDocument::create('addDialog');
            $this->addDialog->cancelable(false);

            $dataContainer = FormContainer::create('addDialogData');
            $this->addDialog->appendChild($dataContainer);

            if (WCF::getUser()->userID) {
                $dataContainer->appendChildren([
                        SingleSelectionFormField::create('character')
                        ->label('rp.event.raid.attendee.character')
                        ->required()
                        ->options(function () {
                            $characters = $this->event->getController()->getContentData('characters');

                            $options = [];
                            foreach ($characters as $id => $character) {
                                $options[] = [
                                    'depth' => 0,
                                    'label' => $character['characterLabel'],
                                    'value' => $id,
                                ];
                            }
                            return $options;
                        }, true),
                        SingleSelectionFormField::create('status')
                        ->label('rp.event.raid.status')
                        ->required()
                        ->options($this->event->getController()->getContentData('raidStatus'))
                ]);
            } else {
                $dataContainer->appendChildren([
                        TextFormField::create('characterName')
                        ->label('rp.event.raid.attendee.character')
                        ->required()
                        ->autoFocus()
                        ->maximumLength(100),
                        EmailFormField::create('eMail')
                        ->label('rp.event.raid.attendee.email')
                        ->required()
                        ->autoFocus(),
                        SingleSelectionFormField::create('roleID')
                        ->label('rp.role.title')
                        ->required()
                        ->options(['' => 'wcf.global.noSelection'] + RoleCache::getInstance()->getRoles())
                        ->addValidator(new FormFieldValidator('uniqueness', function (SingleSelectionFormField $formField) {
                                    $value = $formField->getSaveValue();

                                    if (empty($value)) {
                                        $formField->addValidationError(new FormFieldValidationError('empty'));
                                    } else {
                                        $role = RoleCache::getInstance()->getRoleByID($value);
                                        if ($role === null) {
                                            $formField->addValidationError(new FormFieldValidationError(
                                                    'invalid',
                                                    'rp.role.error.invalid'
                                            ));
                                        }
                                    }
                                })),
                        SingleSelectionFormField::create('classificationID')
                        ->label('rp.classification.title')
                        ->required()
                        ->options(['' => 'wcf.global.noSelection'] + ClassificationCache::getInstance()->getClassifications())
                        ->addValidator(new FormFieldValidator('uniqueness', function (SingleSelectionFormField $formField) {
                                    $value = $formField->getSaveValue();

                                    if (empty($value)) {
                                        $formField->addValidationError(new FormFieldValidationError('empty'));
                                    } else {
                                        $role = ClassificationCache::getInstance()->getClassificationByID($value);
                                        if ($role === null) {
                                            $formField->addValidationError(new FormFieldValidationError(
                                                    'invalid',
                                                    'rp.classification.error.invalid'
                                            ));
                                        }
                                    }
                                })),
                ]);
            }

            $dataContainer->appendChild(
                TextFormField::create('notes')
                    ->label('rp.event.raid.attendee.notes')
                    ->autoFocus()
                    ->maximumLength(255)
            );

            $this->addDialog->build();

            if (!WCF::getUser()->userID) {
                $this->addDialog->getButton('submitButton')->label('rp.event.raid.button.attendee.guest');
            }
        }

        return $this->addDialog;
    }

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $attendeeID = \reset($this->objectIDs);

        if ($attendeeID) {
            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->setObjectIDs([$attendeeID]);
            $attendeeList->readObjects();
            $attendee = $attendeeList->getSingleObject();

            if ($attendee) {
                WCF::getTPL()->assign('attendee', $attendee);
            } else {
                WCF::getTPL()->assign('unknownAttendee', true);
            }
        } else {
            WCF::getTPL()->assign('unknownAttendee', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('raidAttendeePreview', 'rp'),
        ];
    }

    /**
     * Returns the template for the attendee update status.
     */
    public function loadUpdateStatus(): array
    {
        $statusData = [];

        if ($this->attendee->getEvent()->getController()->isLeader()) {
            $statusData[EventRaidAttendee::STATUS_CONFIRMED] = WCF::getLanguage()->get('rp.event.raid.container.confirmed');
        }

        $statusData[EventRaidAttendee::STATUS_LOGIN] = WCF::getLanguage()->get('rp.event.raid.container.login');
        $statusData[EventRaidAttendee::STATUS_RESERVE] = WCF::getLanguage()->get('rp.event.raid.container.reserve');
        $statusData[EventRaidAttendee::STATUS_LOGOUT] = WCF::getLanguage()->get('rp.event.raid.container.logout');

        return [
            'template' => WCF::getTPL()->fetch('eventRaidAttendeeStatusDialog', 'rp', [
                'statusData' => $statusData,
            ])
        ];
    }

    /**
     * Saves the attendee add dialog.
     */
    public function submitAddDialog(): ?array
    {
        if ($this->getAddDialog()->hasValidationErrors()) {
            return [
                'dialog' => $this->getAddDialog()->getHtml(),
                'formId' => 'addDialog'
            ];
        }

        $formData = $this->getAddDialog()->getData();

        if (WCF::getUser()->userID) {
            $characterID = $formData['data']['character'];

            $parameters = [
                'characterID' => $characterID,
                'eventID' => $this->event->eventID,
                'saveData' => [],
            ];
            EventHandler::getInstance()->fireAction($this, 'submitAddDialog', $parameters);

            if ($parameters['characterID'] === null) $saveData = $parameters['saveData'];
            else {
                $character = CharacterRuntimeCache::getInstance()->getObject($formData['data']['character']);

                $saveData = [
                    'characterID' => $character->characterID,
                    'characterName' => $character->characterName,
                    'classificationID' => $character->classificationID,
                    'roleID' => $character->roleID,
                ];
            }

            $saveData['status'] = $formData['data']['status'];
        } else {
            $saveData = [
                'characterName' => $formData['data']['characterName'],
                'email' => $formData['data']['eMail'],
                'classificationID' => $formData['data']['classificationID'],
                'roleID' => $formData['data']['roleID'],
                'status' => EventRaidAttendee::STATUS_LOGIN,
            ];
        }

        $saveData['eventID'] = $this->event->eventID;
        $saveData['notes'] = $formData['data']['notes'];

        $action = new EventRaidAttendeeAction([], 'create', ['data' => $saveData]);
        /** @var EventRaidAttendee $attendee */
        $attendee = $action->executeAction()['returnValues'];

        $distributionID = 0;
        switch ($this->event->distributionMode) {
            case 'class':
                $distributionID = $attendee->classificationID;
                break;
            case 'role':
                $distributionID = $attendee->roleID;
                break;
        }

        return [
            'attendeeId' => $attendee->attendeeID,
            'distributionId' => $distributionID,
            'status' => $attendee->status,
            'template' => WCF::getTPL()->fetch('eventRaidAttendeeItems', 'rp', [
                'attendee' => $attendee,
                'event' => $this->event,
                '__availableDistributionID' => $distributionID,
            ])
        ];
    }

    /**
     * Unmarks attendees.
     */
    protected function unmarkItems(array $attendeeIDs = [])
    {
        if (empty($attendeeIDs)) {
            foreach ($this->getObjects() as $attendee) {
                $attendeeIDs[] = $attendee->attendeeID;
            }
        }

        if (!empty($attendeeIDs)) {
            ClipboardHandler::getInstance()->unmark($attendeeIDs, ClipboardHandler::getInstance()->getObjectTypeID('info.daries.rp.raid.attendee'));
        }
    }

    public function updateStatus(): array
    {
        foreach ($this->objects as $editor) {
            $update = [
                'status' => $this->parameters['status'],
            ];

            switch ($editor->getEvent()->distributionMode) {
                case 'role':
                    if ($this->parameters['distributionID']) {
                        $update['roleID'] = $this->parameters['distributionID'];
                    }
                    break;
            }

            $editor->update($update);

            if ($editor->getCharacter()?->userID) {
                UserNotificationHandler::getInstance()->fireEvent(
                    'status',
                    'info.daries.rp.raid.event.notification',
                    new EventRaidUserNotificationObject($editor->getEvent()),
                    [$editor->getCharacter()->userID],
                    ['objectID' => $editor->getEvent()->eventID]
                );
            }
        }

        $this->unmarkItems();

        return [
            'status' => $this->parameters['status'],
        ];
    }

    /**
     * Validates the 'createAddDialog' action.
     */
    public function validateCreateAddDialog(): void
    {
        $this->readBoolean('eventID');

        if (!WCF::getSession()->getPermission('user.rp.canParticipate')) {
            throw new PermissionDeniedException();
        }

        $this->event = EventRuntimeCache::getInstance()->getObject($this->parameters['eventID']);
        if ($this->event === null) {
            throw new UserInputException('eventID');
        }
    }

    /**
     * @inheritDoc
     */
    public function validateDelete(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        /** @var EventRaidAttendeeEditor $attendee */
        foreach ($this->objects as $attendee) {
            if (!$attendee->getCharacter() || $attendee->getCharacter()->userID !== WCF::getUser()->userID) {
                if (!$attendee->getEvent()->canDelete()) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        WCF::getSession()->checkPermissions(['user.rp.canViewCharacterProfile']);

        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }

    /**
     * Validates the `loadUpdateStatus` action.
     * 
     * @throws  UserInputException
     */
    public function validateLoadUpdateStatus(): void
    {
        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }

        $attendeeID = \reset($this->objectIDs);
        $this->attendee = EventRaidAttendeeRuntimeCache::getInstance()->getObject($attendeeID);
        if ($this->attendee === null) {
            throw new UserInputException('objectIDs');
        }

        if (!$this->attendee->getCharacter() ||
            $this->attendee->getCharacter()->userID != WCF::getUser()->userID ||
            $this->attendee->getEvent()->isClosed ||
            $this->attendee->getEvent()->getController()->isExpired()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates the 'submitAddDialong' action.
     */
    public function validateSubmitAddDialog(): void
    {
        $this->readInteger('eventID');

        if (!WCF::getSession()->getPermission('user.rp.canParticipate')) {
            throw new PermissionDeniedException();
        }

        $this->event = EventRuntimeCache::getInstance()->getObject(($this->parameters['eventID']));
        if ($this->event === null) {
            throw new UserInputException('eventID');
        }

        $dialog = $this->getAddDialog();
        $dialog->requestData($this->parameters['data']);
        $dialog->readValues();
        $dialog->validate();
    }

    /**
     * Validates the `updateStatus` action.
     * 
     * @throws  UserInputException
     */
    public function validateUpdateStatus(): void
    {
        $this->readInteger('distributionID', true);
        $this->readInteger('status', true);

        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        if (!\in_array(
                $this->parameters['status'],
                [
                    EventRaidAttendee::STATUS_CONFIRMED,
                    EventRaidAttendee::STATUS_LOGIN,
                    EventRaidAttendee::STATUS_RESERVE,
                    EventRaidAttendee::STATUS_LOGOUT,
                ]
            )) {
            throw new UserInputException('status');
        }

        /** @var EventRaidAttendeeEditor $attendee */
        foreach ($this->objects as $attendee) {
            if (!$attendee->getCharacter() || $attendee->getCharacter()->userID !== WCF::getUser()->userID) {
                if (!$attendee->getEvent()->canEdit()) {
                    throw new PermissionDeniedException();
                }
            }
        }
    }
}
