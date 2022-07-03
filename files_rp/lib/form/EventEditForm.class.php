<?php

namespace rp\form;

use rp\data\event\Event;
use wcf\data\object\type\ObjectTypeCache;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
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
 * Shows the event edit form.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Form
 */
class EventEditForm extends EventAddForm
{
    /**
     * @inheritDoc
     */
    public $formAction = 'edit';

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        AbstractFormBuilderForm::readParameters();

        if (isset($_REQUEST['id'])) {
            $this->formObject = new Event($_REQUEST['id']);
            if (!$this->formObject->eventID) {
                throw new IllegalLinkException();
            }
        }

        $this->objectTypeID = $this->formObject->objectTypeID;
        $this->eventController = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID);
        $this->eventController->getProcessor()->setEvent($this->formObject);

        if (!$this->formObject->canEdit() && !$this->eventController->getProcessor()->isLeader()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * @inheritDoc
     */
    public function saved(): void
    {
        AbstractFormBuilderForm::saved();
    }

    /**
     * @inheritDoc
     */
    protected function setFormObjectData()
    {
        parent::setFormObjectData();

        if (!empty($_POST)) {
            return;
        }

        $this->eventController->getProcessor()->setFormObjectData($this->form);
    }
}
