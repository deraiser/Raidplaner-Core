<?php

namespace rp\page;

use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventCache;
use rp\data\raid\RaidList;
use wcf\page\MultipleLinkPage;
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
 * Shows the raids page.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Page
 */
class RaidListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $itemsPerPage = 60;

    /**
     * @inheritDoc
     */
    public $objectListClassName = RaidList::class;

    /**
     * raid event object
     */
    public ?RaidEvent $raidEvent = null;

    /**
     * @inheritDoc
     */
    public $sortField = 'date';

    /**
     * @inheritDoc
     */
    public $sortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'raidEvent' => $this->raidEvent,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if ($this->raidEvent) {
            $this->objectList->getConditionBuilder()->add('raidEventID = ?', [$this->raidEvent->eventID]);
        }

        if (RP_ENABLE_ITEM) {
            $this->objectList->sqlSelects = "(
                SELECT  COUNT(*)
                FROM    rp" . WCF_N . "_item_to_raid
                WHERE   raidID = raid.raidID
            ) AS items";
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['raidEventID'])) {
            $raidEventID = \intval($_REQUEST['raidEventID']);
            $this->raidEvent = RaidEventCache::getInstance()->getRaidEventByID($raidEventID);
        }
    }
}
