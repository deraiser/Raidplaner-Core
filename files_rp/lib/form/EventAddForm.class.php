<?php

namespace rp\form;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\data\game\GameCache;
use rp\data\raid\event\RaidEventCache;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
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
 * Shows the event add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Form
 */
class EventAddForm extends AbstractFormBuilderForm
{
    /**
     * @var ObjectType[]
     */
    public $availableEventControllers = [];

    /**
     * event controller
     */
    public ObjectType $eventController;

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canCreateEvent'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = EventAction::class;

    /**
     * @inheritDoc
     */
    public $objectEditLinkApplication = 'rp';

    /**
     * object type id
     */
    public int $objectTypeID = 0;

    /**
     * preset event object
     */
    public ?Event $presetEvent = null;

    /**
     * preset event id
     */
    public int $presetEventID = 0;

    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'presetEvent' => $this->presetEvent,
            'presetEventID' => $this->presetEventID,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $this->eventController->getProcessor()->createForm($this->form);
    }

    /**
     * Reads basic event parameters controlling.
     */
    protected function readEventControllerSetting(): void
    {
        if (!empty($_REQUEST['objectTypeID'])) $this->objectTypeID = \intval($_REQUEST['objectTypeID']);

        $availableEventControllers = ObjectTypeCache::getInstance()->getObjectTypes('info.daries.rp.eventController');
        // work-around to force adding event via dialog overlay
        if (empty($_POST) && !isset($_REQUEST['objectTypeID'])) {
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('Calendar', ['application' => 'rp', 'showEventAddDialog' => 1]));
            exit;
        }

        if (!$this->objectTypeID) $this->objectTypeID = (\current($availableEventControllers))->objectTypeID;
        $this->eventController = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
    }

    public function readData()
    {
        parent::readData();

        if ($this->presetEventID) {
            $this->form->updatedObject($this->presetEvent, true);

            $this->eventController->getProcessor()->setEvent($this->presetEvent);
            $this->eventController->getProcessor()->setFormObjectData($this->form);
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        // preset event
        if (isset($_GET['presetEventID'])) $this->presetEventID = \intval($_GET['presetEventID']);
        if ($this->presetEventID) {
            $this->presetEvent = new Event($this->presetEventID);
            if (!$this->presetEvent->eventID) {
                throw new IllegalLinkException();
            } else if (!$this->presetEvent->canEdit()) {
                throw new PermissionDeniedException();
            }

            $_REQUEST['objectTypeID'] = $this->presetEvent->objectTypeID;
        }

        $this->readEventControllerSetting();

        if ($this->eventController->objectType === 'info.daries.rp.event.raid') {
            $raidEvents = RaidEventCache::getInstance()->getRaidEvents();
            if (!\count($raidEvents)) {
                HeaderUtil::delayedRedirect(
                    LinkHandler::getInstance()->getLink(
                        'Calendar',
                        [
                            'application' => 'rp',
                        ]
                    ),
                    WCF::getLanguage()->getDynamicVariable(
                        'rp.event.raid.noRaidEvents',
                        [
                            'game' => GameCache::getInstance()->getCurrentGame(),
                        ]
                    ),
                    10,
                    'error'
                );
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
        } else if ($this->formAction === 'edit') {
            $action = 'update';
        }

        $formData = $this->form->getData();
        if (!isset($formData['data'])) $formData['data'] = [];
        $formData['data'] = \array_merge($this->additionalFields, $formData['data']);

        /** @var AbstractDatabaseObjectAction objectAction */
        $this->objectAction = new $this->objectActionClass(
            \array_filter([$this->formObject]),
            $action,
            $this->eventController->getProcessor()->saveForm($formData)
        );
        $this->objectAction->executeAction();

        $this->saved();
    }

    /**
     * @inheritDoc
     */
    public function saved(): void
    {
        AbstractForm::saved();

        /** @var Event $event */
        $event = $this->objectAction->getReturnValues()['returnValues'];

        if ($event->isDisabled) {
            HeaderUtil::delayedRedirect(LinkHandler::getInstance()->getLink('Calendar', [
                    'application' => 'rp'
                ]), WCF::getLanguage()->getDynamicVariable('rp.event.moderation.redirect'), 30);
        } else {
            HeaderUtil::redirect($event->getLink());
        }
        exit;
    }

    /**
     * @inheritDoc
     */
    protected function setFormAction()
    {
        $parameters = [];
        if ($this->formObject !== null) {
            if ($this->formObject instanceof IRouteController) {
                $parameters['object'] = $this->formObject;
            } else {
                $object = $this->formObject;

                $parameters['id'] = $object->{$object::getDatabaseTableIndexName()};
            }
        } else {
            $parameters['objectTypeID'] = $this->objectTypeID;
        }

        $this->form->action(LinkHandler::getInstance()->getControllerLink(static::class, $parameters));
    }
}
