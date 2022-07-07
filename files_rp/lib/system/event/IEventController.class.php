<?php

namespace rp\system\event;

use rp\data\event\Event;
use wcf\system\form\builder\IFormDocument;

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
 * Default interface for event controllers.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
interface IEventController
{

    /**
     * Creates the form object.
     * 
     * This is the method that is intended to be overwritten by child classes
     * to add the form containers and fields.
     */
    public function createForm(IFormDocument $form): void;

    /**
     * Returns the template in the main section of the event.
     */
    public function getContent(): string;

    /**
     * Returns the database object of this event.
     */
    public function getEvent(): ?Event;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size = null): string;

    /**
     * Returns the moderation template name for this event object type.
     */
    public function getModerationTemplate(): string;

    /**
     * Returns the object type name.
     */
    public function getObjectTypeName(): string;

    /**
     * Returns the title of the event.
     */
    public function getTitle(): string;

    /**
     * Returns true if you participate in the event, false otherwise.
     */
    public function hasLogin(): bool;
    
    /**
     * Checks whether time for this event is expired
     */
    public function isExpired(): bool;

    /**
     * Returns the data of the form which should be saved.
     */
    public function saveForm(array $formData): array;

    /**
     * Sets the database object of this event.
     */
    public function setEvent(Event $event): void;

    /**
     * Sets the form data based on the form object.
     */
    public function setFormObjectData(IFormDocument $form, array $fields = []): void;
}
