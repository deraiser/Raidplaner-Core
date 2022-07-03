<?php

namespace rp\data\modification\log;

use rp\data\event\Event;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\modification\log\IViewableModificationLog;
use wcf\data\modification\log\ModificationLog;
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
 * Provides a viewable event modification log.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Modification\Log
 *
 * @method      ModificationLog     getDecoratedObject()
 * @mixin       ModificationLog
 */
class ViewableEventModificationLog extends DatabaseObjectDecorator implements IViewableModificationLog
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ModificationLog::class;

    /**
     * event object
     */
    public ?Event $event = null;

    /**
     * Sets the event object.
     */
    public function setEvent(Event $event): void
    {
        if ($event->eventID === $this->objectID) {
            $this->event = $event;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAffectedObject(): ?Event
    {
        return $this->event;
    }

    /**
     * Returns readable representation of current log entry.
     * 
     * @return	string
     */
    public function __toString()
    {
        return WCF::getLanguage()->getDynamicVariable('rp.event.log.' . $this->action , [
                'additionalData' => $this->additionalData,
                'event' => $this->event
        ]);
    }
}
