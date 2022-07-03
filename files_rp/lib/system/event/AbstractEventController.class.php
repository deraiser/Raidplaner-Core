<?php

namespace rp\system\event;

use rp\data\event\Event;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\EventHandler;

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
 * Default implementation for event controllers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
abstract class AbstractEventController implements IEventController
{
    /**
     * database object of this event
     */
    protected ?Event $event = null;

    /**
     * object type name
     */
    protected string $objectTypeName = '';

    /**
     * ids of the fields containing object data
     */
    protected array $savedFields = [];

    /**
     * @inheritDoc
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(?int $size = null): string
    {
        $iconSize = '';
        if ($size) $iconSize = 'icon' . $size;

        return '<span class="icon ' . $iconSize . ' fa-calendar-o"></span>';
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeName(): string
    {
        return $this->objectTypeName;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function hasLogin(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isExpired(): bool
    {
        return false;
    }

    /**
     * Prepares save of event item.
     */
    protected function prepareSave(array &$formData): void
    {
        // does nothing
    }

    /**
     * @inheritDoc
     */
    public function saveForm(array $formData): array
    {
        if (empty($this->savedFields)) return $formData;

        $this->prepareSave($formData);

        $data = [];
        foreach ($this->savedFields as $field) {
            if (isset($formData['data'][$field])) {
                $data[$field] = $formData['data'][$field];
                unset($formData['data'][$field]);
            }
        }

        $data['objectTypeID'] = (ObjectTypeCache::getInstance()->getObjectTypeByName('info.daries.rp.eventController', $this->objectTypeName))->objectTypeID;

        $data['additionalData'] = \serialize($formData['data']);
        unset($formData['data']);

        return \array_merge(['data' => $data], $formData);
    }

    /**
     * @inheritDoc
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    /**
     * Creates a new instance of AbstractEventController.
     */
    public function __construct()
    {
        EventHandler::getInstance()->fireAction($this, '__construct');
    }
}
