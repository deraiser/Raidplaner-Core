<?php

namespace rp\data\raid\event;

use rp\system\cache\builder\RaidEventCacheBuilder;
use wcf\system\SingletonFactory;

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
 * Manages the raid event cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Event
 */
class RaidEventCache extends SingletonFactory
{
    /**
     * cached raid events
     * @var RaidEvent[]
     */
    protected $cachedRaidEvents = [];

    /**
     * Returns the raid event with the given raid event id or `null` if no such raid event exists.
     */
    public function getRaidEventByID(int $raidEventID): ?RaidEvent
    {
        return $this->cachedRaidEvents[$raidEventID] ?? null;
    }

    /**
     * Returns all raid events.
     * 
     * @return	RaidEvent[]
     */
    public function getRaidEvents(): array
    {
        return $this->cachedRaidEvents;
    }

    /**
     * Returns the raid events with the given raid event ids.
     */
    public function getRaidEventsByIDs(array $raidEventIDs): array
    {
        $events = [];

        foreach ($raidEventIDs as $raidEventID) {
            $events[] = $this->getRaidEventByID($raidEventID);
        }

        return $events;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedRaidEvents = RaidEventCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID]);
    }
}
