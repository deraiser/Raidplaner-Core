<?php

namespace rp\system\calendar;

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
 * Displays a specific day in the month.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Calendar
 */
final class Day
{
    /**
     * day
     */
    protected int $day = 0;

    /**
     * day of the week
     */
    protected ?int $dayOfTheWeek = null;

    /**
     * month object
     */
    protected Month $month;

    /**
     * next day
     */
    protected ?Day $nextDay = null;

    /**
     * previous day
     */
    protected ?Day $previousDay = null;

    /**
     * Week number of the day
     */
    protected ?int $week = null;

    /**
     * Returns the day.
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * Returns the day in the week
     */
    public function getDayOfTheWeek(): int
    {
        if ($this->dayOfTheWeek === null) {
            $date = new \DateTime();
            $date->setDate($this->getMonth()->getYear(), $this->month->getMonth(), $this->getDay());
            $this->dayOfTheWeek = $date->format('w');
        }

        return $this->dayOfTheWeek;
    }

    /**
     * Returns the current month object of the tag.
     */
    public function getMonth(): Month
    {
        return $this->month;
    }

    /**
     * Returns the next day.
     */
    public function getNextDay(): Day
    {
        if ($this->nextDay === null) {
            if ($this->isLastDayOfMonth()) {
                $this->nextDay = $this->getMonth()->getNextMonth()->getFirstDayOfMonth();
            } else {
                $this->nextDay = new Day($this->getDay() + 1, $this->getMonth());
            }
        }

        return $this->nextDay;
    }

    /**
     * Returns the previous day.
     */
    public function getPreviousDay(): Day
    {
        if ($this->previousDay === null) {
            if ($this->isFirstDayOfMonth()) {
                $this->previousDay = $this->getMonth()->getPreviousMonth()->getLastDayOfMonth();
            } else {
                $this->previousDay = new Day($this->getDay() - 1, $this->getMonth());
            }
        }

        return $this->previousDay;
    }

    public function getWeek(): int
    {
        if ($this->week === null) {
            $date = new \DateTime();
            $date->setDate($this->getMonth()->getYear(), $this->month->getMonth(), $this->getDay());
            $this->week = $date->format('W');
        }

        return $this->week;
    }

    /**
     * Returns true if the given day is equal.
     */
    public function isCurrentDay(Day $day): bool
    {
        if ($this->__toString() == $day->__toString()) return true;
        return false;
    }

    /**
     * Returns true, if the day is the first day of the month.
     */
    public function isFirstDayOfMonth(): bool
    {
        return $this->getDay() == 1;
    }

    /**
     * Returns true, if the day is the first day of the week.
     */
    public function isFirstDayOfWeek(): bool
    {
        return $this->getDayOfTheWeek() == DateUtil::getFirstDayOfTheWeek();
    }

    /**
     * Returns true, if the day is the last day in the month.
     */
    public function isLastDayOfMonth(): bool
    {
        return $this->getDay() == $this->getMonth()->getLastDayOfMonth()->getDay();
    }

    /**
     * Returns true, if the day is the last day in the week.
     */
    public function isLastDayOfWeek(): bool
    {
        return $this->getDayOfTheWeek() == (DateUtil::getFirstDayOfTheWeek() == 1 ? 0 : 6);
    }

    public function __construct(int $day, Month $month)
    {
        $this->day = $day;
        $this->month = $month;
    }

    public function __toString()
    {
        return $this->getMonth()->getYear() . '-' . ($this->getMonth()->getMonth() < 10 ? '0' : '') . $this->getMonth()->getMonth() . '-' . ($this->getDay() < 10 ? '0' : '') . $this->getDay();
    }
}
