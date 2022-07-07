<?php

namespace rp\system\user\notification\event;

use rp\system\user\notification\object\EventRaidUserNotificationObject;
use wcf\system\user\notification\event\AbstractUserNotificationEvent;

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
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      EventRaidUserNotificationObject     getUserNotificationObject()
 */
class EventRaidStatusUserNotificationEvent extends AbstractUserNotificationEvent
{

    /**
     * @inheritDoc
     */
    public function checkAccess(): bool
    {
        return $this->getUserNotificationObject()->canRead();
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . TIME_NOW);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getUserNotificationObject()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return $this->getLanguage()->getDynamicVariable('rp.user.notification.raid.event.status.message', [
                'author' => $this->author,
                'event' => $this->userNotificationObject,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->getLanguage()->get('rp.user.notification.raid.event.status.title');
    }

    /**
     * @inheritDoc
     */
    public function supportsEmailNotification(): bool
    {
        return false;
    }
}
