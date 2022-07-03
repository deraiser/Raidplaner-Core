<?php

namespace rp\system\calendar;

use rp\data\event\AccessibleEventList;
use wcf\system\WCF;
use wcf\util\DateUtil;

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
 * Represents a calendar.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Calendar
 */
final class Calendar
{
    /**
     * days objects
     * @var Day[]
     */
    protected array $days = [];

    /**
     * end timestamp view
     */
    protected int $end = 0;

    /**
     * events of this month
     */
    protected array $events = [];

    /**
     * month object
     */
    protected Month $month;

    /**
     * start timestamp view
     */
    protected int $start = 0;

    /**
     * Creates a new Calendar object.
     */
    public function __construct(Month $month)
    {
        $this->month = $month;

        $day = $this->getMonth()->getFirstDayOfMonth();
        while (!$day->isFirstDayOfWeek()) {
            $day = $day->getPreviousDay();
            \array_unshift($this->days, $day);
        }

        $this->days = \array_merge($this->days, $this->getMonth()->getDays());

        $day = $this->getMonth()->getLastDayOfMonth();
        while (!$day->isLastDayOfWeek()) {
            $day = $day->getNextDay();
            $this->days[] = $day;
        }

        $start = new \DateTime($this->getDays()[0] . ' 00:00', WCF::getUser()->getTimeZone());
        $this->start = $start->getTimestamp();

        $end = new \DateTime($this->getDays()[\count($this->getDays()) - 1] . ' 23:59', WCF::getUser()->getTimeZone());
        $this->end = $end->getTimestamp();
    }

    /**
     * Performs the actual calculation.
     */
    public function calculate(AccessibleEventList $eventList): void
    {
        $hasEventIDs = $multiEventDays = $singleEvents = [];

        foreach ($this->getDays() as $day) {
            $index = $day->__toString();
            $this->events[$index] = [];
            $events = $eventList->getEventsByDay($index);

            /** @var Event $event */
            foreach ($events as $event) {
                $eventID = $event->eventID;

                if (isset($hasEventIDs[$eventID])) continue;

                $eventDays = $event->getEventDays();
                if (\count($eventDays) == 1) {
                    if ($event->isFullDay) {
                        $position = 0;
                        while (true) {
                            $found = false;
                            if (isset($multiEventDays[$index][$position])) {
                                $found = true;
                            }

                            if (!$found) break;
                            else $position++;
                        }

                        $multiEventDays[$index][$position] = $event;
                    } else {
                        if (!isset($singleEvents[$index])) $singleEvents[$index] = [];
                        $singleEvents[$index][] = $event;
                    }
                } else {
                    $position = 0;
                    while (true) {
                        $found = false;
                        foreach ($eventDays as $eventDay) {
                            $eventIndex = $eventDay->__toString();
                            if (isset($multiEventDays[$eventIndex][$position])) {
                                $found = true;
                            }
                        }

                        if (!$found) break;
                        else $position++;
                    }

                    $maxCount = \count($eventDays);
                    $count = 1;
                    foreach ($eventDays as $eventDay) {
                        $eventIndex = $eventDay->__toString();
                        $dayEvent = clone $event;

                        if ($count == 1) {
                            $dayEvent->cssMultipleEvent = 'rpEventStart';
                        } else if ($count == $maxCount) {
                            $dayEvent->cssMultipleEvent = 'rpEventEnd';
                        } else {
                            $dayEvent->cssMultipleEvent = 'rpEventCenter';
                        }

                        if (!isset($multiEventDays[$eventIndex])) $multiEventDays[$eventIndex] = [];

                        if ($position > 0) {
                            for ($i = $position - 1; $i >= 0; $i--) {
                                if (!isset($multiEventDays[$eventIndex][$i])) {
                                    if ($eventIndex) $multiEventDays[$eventIndex][$i] = ':empty';
                                }
                            }
                        }

                        $multiEventDays[$eventIndex][$position] = $dayEvent;

                        $count++;
                    }
                }

                $hasEventIDs[$eventID] = $eventID;
            }
        }

        if (!empty($multiEventDays)) {
            foreach ($this->getDays() as $day) {
                $index = $day->__toString();

                if (isset($multiEventDays[$index])) {
                    if ($day->isFirstDayOfWeek()) {
                        $pos = 0;

                        while (true) {
                            if (isset($multiEventDays[$index][$pos]) && $multiEventDays[$index][$pos] === ':empty') {
                                $nextDay = $day->getNextDay();
                                $nextDayIndex = $nextDay->__toString();
                                while (true) {
                                    if (isset($multiEventDays[$nextDayIndex][$pos]) && $multiEventDays[$nextDayIndex][$pos] === ':empty') {
                                        unset($multiEventDays[$nextDayIndex][$pos]);

                                        if ($nextDay->isLastDayOfWeek()) break;

                                        $nextDay = $nextDay->getNextDay();
                                        $nextDayIndex = $nextDay->__toString();
                                    } else break;
                                }
                                unset($multiEventDays[$index][$pos]);
                                $pos++;
                            } else break;
                        }
                    }
                }
            }

            foreach ($this->getDays() as $day) {
                $index = $day->__toString();

                if (isset($multiEventDays[$index])) {
                    foreach ($multiEventDays[$index] as $position => $event) {
                        $this->events[$index][$position] = $event;
                    }
                }
            }
        }

        if (!empty($singleEvents)) {
            foreach ($this->getDays() as $day) {
                $index = $day->__toString();
                if (isset($singleEvents[$index])) {
                    foreach ($singleEvents[$index] as $position => $event) {
                        $this->events[$index][] = $event;
                    }
                }
            }
        }
    }

    /**
     * Returns all days.
     * 
     * @return Day[]
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * Return end view timestamp.
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Returns the events of the specified day.
     */
    public function getEvents(string $day): array
    {
        return $this->events[$day] ?? [];
    }

    /**
     * Returns the month.
     */
    public function getMonth(): Month
    {
        return $this->month;
    }

    /**
     * Return start view timestamp.
     */
    public function getStart(): int
    {
        return $this->start;
    }
}
