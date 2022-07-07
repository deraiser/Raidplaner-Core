<?php

namespace rp\data\point\account;

use rp\system\cache\builder\PointAccountCacheBuilder;
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
 * Manages the point account cache.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Point\Account
 */
class PointAccountCache extends SingletonFactory
{
    /**
     * cached point accounts
     * @var PointAccount[]
     */
    protected array $cachedPointAccounts = [];

    /**
     * cached raid events
     */
    protected mixed $cachedRaidEvents = [];

    /**
     * Returns the point account with the given point account id or `null` if no such point account exists.
     * 
     * @param	int                 $pointAccountID
     * @return  PointAccount|null
     */
    public function getPointAccountByID(int $pointAccountID): ?PointAccount
    {
        return $this->cachedPointAccounts[$pointAccountID] ?? null;
    }

    /**
     * Returns all point accounts.
     * 
     * @return  PointAccount[]
     */
    public function getPointAccounts(): array
    {
        return $this->cachedPointAccounts;
    }

    /**
     * Returns the point accounts with the given point account ids.
     * 
     * @return	PointAccount[]
     */
    public function getPointAccountsByIDs(array $pointAccountIDs): array
    {
        $accounts = [];

        foreach ($pointAccountIDs as $pointAccountID) {
            $accounts[] = $this->getPointAccountByID($pointAccountID);
        }

        return $accounts;
    }

    /**
     * Returns all raid events associated with the point account ID.
     */
    public function getRaidEventsByID(int $pointAccountID): array
    {
        return $this->cachedRaidEvents[$pointAccountID] ?? [];
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedPointAccounts = PointAccountCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'accounts');
        $this->cachedRaidEvents = PointAccountCacheBuilder::getInstance()->getData(['gameID' => RP_DEFAULT_GAME_ID], 'raidEvents');
    }
}
