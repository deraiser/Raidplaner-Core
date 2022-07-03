<?php

namespace rp\system\event;

use wcf\system\cache\runtime\UserProfileRuntimeCache;
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
 * Appointment event implementation for event controllers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
class AppointmentEventController extends DefaultEventController
{
    /**
     * @inheritDoc
     */
    protected string $objectTypeName = 'info.daries.rp.event.appointment';

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        $accepted = $canceled = $maybe = [];

        if ($this->getEvent()->appointments) {
            foreach ($this->getEvent()->appointments as $status => $userIDs) {
                foreach ($userIDs as $userID) {
                    switch ($status) {
                        case 'accepted':
                            $accepted[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                        case 'canceled':
                            $canceled[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                        case 'maybe':
                            $maybe[] = UserProfileRuntimeCache::getInstance()->getObject($userID);
                            break;
                    }
                }
            }
        }

        WCF::getTPL()->assign([
            'accepted' => $accepted,
            'canceled' => $canceled,
            'maybe' => $maybe
        ]);

        return WCF::getTPL()->fetch('eventAppointment', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function hasLogin(): bool
    {
        if ($this->getEvent()->appointments) {
            foreach ($this->getEvent()->appointments as $userIDs) {
                if (\in_array(WCF::getUser()->userID, $userIDs)) return true;
            }
        }

        return false;
    }
    
    public function isExpired(): bool
    {
        if ($this->getEvent()->startTime < TIME_NOW) return true;
        return false;
    }
}
