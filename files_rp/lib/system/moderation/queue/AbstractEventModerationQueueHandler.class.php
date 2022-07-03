<?php

namespace rp\system\moderation\queue;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\data\event\EventList;
use rp\data\event\ViewableEvent;
use rp\system\cache\runtime\EventRuntimeCache;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\system\moderation\queue\ModerationQueueManager;
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
 * An abstract implementation of IModerationQueueHandler for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Moderation\Queue
 */
class AbstractEventModerationQueueHandler extends AbstractModerationQueueHandler
{
    /**
     * @inheritDoc
     */
    protected $className = Event::class;

    /**
     * @inheritDoc
     */
    protected $objectType = 'info.daries.rp.event';

    /**
     * @inheritDoc
     */
    public function assignQueues(array $queues): void
    {
        $assignments = [];

        foreach ($queues as $queue) {
            $assignUser = false;

            if (WCF::getSession()->getPermission('mod.rp.canModerateEvent')) {
                $assignUser = true;
            }

            $assignments[$queue->queueID] = $assignUser;
        }

        ModerationQueueManager::getInstance()->setAssignment($assignments);
    }

    /**
     * @inheritDoc
     */
    public function getContainerID($objectID): int
    {
        return 0;
    }

    /**
     * Returns a event object by event id or `null` if event id is invalid.
     */
    protected function getEvent(int $objectID): ?Event
    {
        return EventRuntimeCache::getInstance()->getObject($objectID);
    }

    /**
     * Returns the parsed template for the target event.
     */
    protected function getRelatedContent(ViewableModerationQueue $queue): string
    {
        WCF::getTPL()->assign([
            'event' => new ViewableEvent($queue->getAffectedObject()),
        ]);

        return WCF::getTPL()->fetch('moderationEvent', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isValid($objectID): bool
    {
        if ($this->getEvent($objectID) === null) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function populate(array $queues): void
    {
        $objectIDs = [];
        foreach ($queues as $object) {
            $objectIDs[] = $object->objectID;
        }

        // fetch events
        $eventList = new EventList();
        $eventList->setObjectIDs($objectIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        foreach ($queues as $object) {
            if (isset($events[$object->objectID])) {
                $object->setAffectedObject($events[$object->objectID]);
            } else {
                $object->setIsOrphaned();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function removeContent(ModerationQueue $queue, $message): void
    {
        if ($this->isValid($queue->objectID)) {
            $action = new EventAction([$this->getEvent($queue->objectID)], 'trash', ['data' => ['reason' => $message]]);
            $action->executeAction();
        }
    }
}
