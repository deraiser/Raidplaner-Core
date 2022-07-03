<?php

namespace rp\system\moderation\queue\activation;

use rp\data\event\EventAction;
use rp\system\moderation\queue\AbstractEventModerationQueueHandler;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;

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
 * @author      Marco Daries
 * @package     Daries\RP\System\Moderation\Queue\Activation
 */
class EventModerationQueueActivationHandler extends AbstractEventModerationQueueHandler implements IModerationQueueActivationHandler
{
    /**
     * @inheritDoc
     */
    protected $definitionName = 'com.woltlab.wcf.moderation.activation';

    /**
     * @inheritDoc
     */
    public function enableContent(ModerationQueue $queue): void
    {
        if ($this->isValid($queue->objectID) && $this->getEvent($queue->objectID)->isDisabled) {
            $eventAction = new EventAction([$this->getEvent($queue->objectID)], 'enable');
            $eventAction->executeAction();
        }
    }

    /**
     * @inheritDoc
     */
    public function getDisabledContent(ViewableModerationQueue $queue): string
    {
        return $this->getRelatedContent($queue);
    }
}
