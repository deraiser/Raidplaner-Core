<?php

namespace rp\page;

use rp\data\event\AccessibleEventList;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use wcf\data\object\type\ObjectTypeCache;
use wcf\page\AbstractPage;
use wcf\page\MultipleLinkPage;
use wcf\system\event\EventHandler;
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
 * Shows a list of event participations.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Page
 */
class EventParticipationListPage extends MultipleLinkPage
{
    /**
     * events shown
     */
    public array $events = [];

    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canReadEvent'];

    /**
     * @inheritDoc
     */
    public $sortField = 'startTime';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'events' => $this->events,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function countItems(): int
    {
        // call countItems event
        EventHandler::getInstance()->fireAction($this, 'countItems');

        return \count($this->events);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        AbstractPage::readData();

        $eventList = new AccessibleEventList(TIME_NOW, TIME_NOW + 86400 * 365);
        $eventList->readObjects();
        foreach ($eventList as $event) {
            $objectType = ObjectTypeCache::getInstance()->getObjectType($event->objectTypeID);

            $isParticipat = false;
            switch ($objectType->objectType) {
                case 'info.daries.rp.event.appointment':
                    if ($event->appointments) {
                        foreach ($event->appointments as $status => $userIDs) {
                            foreach ($userIDs as $userID) {
                                if ($userID === WCF::getUser()->userID) {
                                    $isParticipat = true;
                                    break 2;
                                }
                            }
                        }
                    }
                    break;
                case 'info.daries.rp.event.raid':
                    $attendeeList = new EventRaidAttendeeList();
                    $attendeeList->getConditionBuilder()->add('event_raid_attendee.eventID = ?', [$event->eventID]);
                    $attendeeList->getConditionBuilder()->add('event_raid_attendee.characterID IS NOT NULL');
                    $attendeeList->getConditionBuilder()->add('member.userID = ?', [WCF::getUser()->userID]);

                    $attendeeList->sqlConditionJoins .= "
                        LEFT JOIN   rp" . WCF_N . "_member member
                        ON          member.characterID = event_raid_attendee.characterID";

                    if ($attendeeList->countObjects()) {
                        $isParticipat = true;
                    }
                    break;
                default:
                    $parameters = [
                        'event' => $event,
                        'isParticipat' => false,
                    ];
                    EventHandler::getInstance()->fireAction($this, 'readEvent', $parameters);
                    $isParticipat = $parameters['isParticipat'];
                    break;
            }

            if ($isParticipat) {
                $this->events[$event->eventID] = $event;
            }
        }

        $this->calculateNumberOfPages();

        $i = 0;
        foreach ($this->events as $eventID => $event) {
            $i++;

            if ($i < $this->startIndex || $i > $this->endIndex) {
                unset($this->events[$eventID]);
                continue;
            }
        }
    }
}
