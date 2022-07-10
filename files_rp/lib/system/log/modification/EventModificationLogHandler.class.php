<?php

namespace rp\system\log\modification;

use rp\data\event\Event;
use rp\data\event\EventList;
use rp\data\modification\log\ViewableEventModificationLog;
use wcf\data\modification\log\ModificationLog;
use wcf\system\log\modification\AbstractExtendedModificationLogHandler;

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
 * Handles event modification logs.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Log\Modification
 */
class EventModificationLogHandler extends AbstractExtendedModificationLogHandler
{
    /**
     * @inheritDoc
     */
    protected $objectTypeName = 'info.daries.rp.event';

    /**
     * Adds a event modification log entry.
     */
    public function add(Event $event, string $action, array $additionalData = []): ModificationLog
    {
        return $this->createLog($action, $event->eventID, null, $additionalData);
    }

    /**
     * Adds a log entry for raid event cancel.
     */
    public function cancel(Event $event): void
    {
        $this->fireNotification($event, $this->add($event, 'cancel'));
    }

    /**
     * Adds a log entry for event delete.
     */
    public function delete(Event $event): void
    {
        $this->add($event, 'delete', [
            'time' => $event->created,
            'username' => $event->username,
        ]);
    }

    /**
     * Adds a log entry for event disabling.
     */
    public function disable(Event $event): void
    {
        $this->add($event, 'disable');
    }

    /**
     * Adds a log entry for event edit.
     */
    public function edit(Event $event, string $reason = ''): void
    {
        $this->add($event, 'edit', ['reason' => $reason]);
    }

    /**
     * Adds a log entry for event enabling.
     */
    public function enable(Event $event): void
    {
        $this->fireNotification($event, $this->add($event, 'enable'));
    }

    /**
     * Fires a moderation notification.
     */
    protected function fireNotification(Event $event, ModificationLog $modificationLog): void
    {
        // TODO
    }

    /**
     * @inheritDoc
     */
    public function getAvailableActions(): array
    {
        return [
            'cancel',
            'delete',
            'disable',
            'edit',
            'enable',
            'restore',
            'trash',
        ];
    }

    /**
     * @inheritDoc
     */
    public function processItems(array $items): array
    {
        $eventIDs = [];
        /** @var ModificationLog $item */
        foreach ($items as $item) {
            $eventIDs[] = $item->objectID;
        }

        $eventList = new EventList();
        $eventList->setObjectIDs($eventIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        foreach ($items as &$item) {
            $item = new ViewableEventModificationLog($item);
            if (isset($events[$item->objectID])) {
                $item->setEvent($events[$item->objectID]);
            }
        }

        return $items;
    }

    /**
     * Adds a log entry for event restore.
     */
    public function restore(Event $event): void
    {
        $this->add($event, 'restore');
    }

    /**
     * Adds a log entry for event soft-delete (trash).
     */
    public function trash(Event $event, string $reason = ''): void
    {
        $this->fireNotification($event, $this->add($event, 'trash', ['reason' => $reason]));
    }
}
