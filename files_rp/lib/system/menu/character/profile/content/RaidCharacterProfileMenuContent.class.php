<?php

namespace rp\system\menu\character\profile\content;

use rp\data\raid\RaidList;
use wcf\system\SingletonFactory;
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
 * Handles character profile raid content.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class RaidCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $raidList = new RaidList();
        $raidList->sqlJoins = "
                LEFT JOIN   rp" . WCF_N . "_raid_attendee raid_attendee
                ON          raid.raidID = raid_attendee.raidID";
        $raidList->getConditionBuilder()->add('raid_attendee.characterID = ?', [$characterID]);
        $raidList->sqlOrderBy = 'raid.date DESC, raid.raidID DESC';

        // load more items than necessary to avoid empty list if some items are invisible for current character
        $raidList->sqlLimit = 10;

        $raidList->readObjects();

        // remove unused items
        $raidList->truncate(6);

        WCF::getTPL()->assign([
            'characterID' => $characterID,
            'lastRaidTime' => $raidList->getLastRaidTime(),
            'raidList' => $raidList,
        ]);

        return WCF::getTPL()->fetch('characterProfileRaid', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
