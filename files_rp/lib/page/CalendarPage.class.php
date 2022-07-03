<?php

namespace rp\page;

use rp\data\event\FilteredEventList;
use rp\system\calendar\Calendar;
use rp\system\calendar\Day;
use rp\system\calendar\Month;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\HeaderUtil;

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
 * Shows the calendar overview page (monthly view).
 *
 * @author      Marco Daries
 * @package     Daries\RP\Page
 */
class CalendarPage extends AbstractPage
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canReadEvent'];

    /**
     * calendar object
     */
    public Calendar $calendar;

    /**
     * month object
     */
    public Month $month;

    /**
     * display 'Add Event' dialog on load
     */
    public int $showEventAddDialog = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        $availableEventControllers = ObjectTypeCache::getInstance()->getObjectTypes('info.daries.rp.eventController');
        \uasort($availableEventControllers, function (ObjectType $a, ObjectType $b) {
            return \strcmp(
            WCF::getLanguage()->get('rp.event.controller.' . $a->objectType),
            WCF::getLanguage()->get('rp.event.controller.' . $b->objectType)
            );
        });

        WCF::getTPL()->assign([
            'availableEventControllers' => $availableEventControllers,
            'calendar' => $this->calendar,
            'currentDay' => $this->currentDay,
            'currentMonth' => $this->currentMonth,
            'month' => $this->month,
            'showEventAddDialog' => $this->showEventAddDialog,
            'shortWeekDays' => DateUtil::getShortWeekDays(),
            'weekDays' => DateUtil::getWeekDays(),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        $this->calendar = new Calendar($this->month);

        $eventList = new FilteredEventList($this->calendar->getStart(), $this->calendar->getEnd());
        $eventList->readObjects();
        $this->calendar->calculate($eventList);

        $date = new \DateTime('@' . TIME_NOW);
        $date->setTimezone(WCF::getUser()->getTimeZone());
        $this->currentMonth = new Month($date->format('n'), $date->format('Y'));
        $this->currentDay = new Day($date->format('j'), $this->currentMonth);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (!empty($_REQUEST['showEventAddDialog'])) $this->showEventAddDialog = 1;

        $month = $year = 0;
        if (isset($_REQUEST['year'])) $year = \intval($_REQUEST['year']);
        if (isset($_REQUEST['month'])) {
            $month = \intval($_REQUEST['month']);
            if ($month < 1 || $month > 12) throw new IllegalLinkException ();
        }

        if (!$year) {
            $date = new \DateTime('@' . TIME_NOW);
            $date->setTimezone(WCF::getUser()->getTimeZone());
            $month = $date->format('n');
            $year = $date->format('Y');
        }
        if (!$month) $month = 1;

        $this->month = new Month($month, $year);

        if (!empty($_POST)) {
            $parameters = [
                'application' => 'rp',
                'month' => $this->month->getMonth(),
                'year' => $this->month->getYear(),
            ];

            if ($this->showEventAddDialog) {
                $parameters['showEventAddDialog'] = 1;
            }

            $url = \http_build_query($_POST, '', '&');
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('Calendar', $parameters, $url));

            exit;
        }
    }
}
