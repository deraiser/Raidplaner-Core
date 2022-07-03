<?php

namespace rp\system\moderation\queue\report;

use rp\system\moderation\queue\AbstractEventModerationQueueHandler;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\report\IModerationQueueReportHandler;

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
 * An implementation of IModerationQueueReportHandler for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Moderation\Queue\Report
 */
class EventModerationQueueReportHandler extends AbstractEventModerationQueueHandler implements IModerationQueueReportHandler
{
    /**
     * @inheritDoc
     */
    protected $definitionName = 'com.woltlab.wcf.moderation.report';

    /**
     * @inheritDoc
     */
    public function canReport($objectID): bool
    {
        if (!$this->isValid($objectID)) {
            return false;
        }

        if (!$this->getEvent($objectID)->canRead()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getReportedContent(ViewableModerationQueue $queue): string
    {
        return $this->getRelatedContent($queue);
    }

    /**
     * @inheritDoc
     */
    public function getReportedObject($objectID)
    {
        return $this->getEvent($objectID);
    }
}
