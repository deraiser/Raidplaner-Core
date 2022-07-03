<?php

namespace rp\data\event;

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
 * Represents a filtered list of events.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 */
class FilteredEventList extends AccessibleEventList
{
    /**
     * Events filtered by date
     */
    protected ?array $events = null;

    /**
     * Creates a new AccessibleEventList object.
     */
    public function __construct(int $start = 0, int $end = 0)
    {
        parent::__construct();

        if ($start && $end) {
            $this->getConditionBuilder()->add(
                '((event.startTime > ? AND event.startTime < ?) OR (event.startTime < ? AND event.endTime >= ?))',
                [$start, $end, $start, $start]
            );
        }
    }

    /**
     * Returns all events of the specified day.
     */
    public function getEventsByDay(string $day): array
    {
        $this->sortFilterEvents();

        return $this->events[$day] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function readObjects(): void
    {
        parent::readObjects();

        $this->sortFilterEvents();
    }

    /**
     * Sorts the events by date
     */
    protected function sortFilterEvents(): void
    {
        if ($this->events === null) {
            $this->events = [];

            foreach ($this->objects as $event) {
                $days = $event->getEventDays();
                foreach ($days as $day) {
                    $index = $day->__toString();
                    if (!isset($this->events[$index])) $this->events[$index] = [];
                    $this->events[$index][] = $event;
                }
            }
        }
    }
}
