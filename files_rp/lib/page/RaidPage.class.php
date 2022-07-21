<?php

namespace rp\page;

use rp\data\classification\ClassificationCache;
use rp\data\item\ItemCache;
use rp\data\point\account\PointAccountCache;
use rp\data\raid\Raid;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\cache\runtime\RaidRuntimeCache;
use rp\util\RPUtil;
use wcf\page\AbstractPage;
use wcf\system\exception\IllegalLinkException;
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
 * Shows the raid page.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Page
 */
class RaidPage extends AbstractPage
{
    /**
     * class distributions
     */
    public array $classDistributions = [];

    /**
     * raid items
     */
    public array $items = [];

    /**
     * raid object
     */
    public Raid $raid;

    /**
     * raid id
     */
    public int $raidID = 0;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'classDistributions' => $this->classDistributions,
            'items' => $this->items,
            'raid' => $this->raid,
            'raidID' => $this->raidID
        ]);
    }

    /**
     * @inheritDoc
     */
    public function readData(): void
    {
        parent::readData();

        $attendees = $this->raid->getAttendees();
        foreach ($attendees as $attendee) {
            $classification = ClassificationCache::getInstance()->getClassificationByID($attendee->classificationID);
            if ($classification === null) continue;

            if (!isset($this->classDistributions[$classification->classificationID])) {
                $this->classDistributions[$classification->classificationID] = [
                    'attendees' => [],
                    'count' => 0,
                    'object' => $classification,
                    'percent' => 0
                ];
            }

            $this->classDistributions[$classification->classificationID]['count']++;
            $this->classDistributions[$classification->classificationID]['attendees'][] = $attendee;
        }

        foreach ($this->classDistributions as $classificationID => $classification) {
            $this->classDistributions[$classificationID]['percent'] = \number_format(($classification['count'] / \count($attendees)) * 100);
        }

        $sql = "SELECT  characterID, itemID, pointAccountID, points
                FROM    rp" . WCF_N . "_item_to_raid item_to_raid
                WHERE   raidID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->raidID]);

        while ($row = $statement->fetchArray()) {
            $this->items[] = [
                'character' => CharacterRuntimeCache::getInstance()->getObject($row['characterID']),
                'item' => ItemCache::getInstance()->getItemByID($row['itemID']),
                'pointAccount' => PointAccountCache::getInstance()->getPointAccountByID($row['pointAccountID']),
                'points' => RPUtil::formatPoints($row['points'])
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        if (isset($_REQUEST['id'])) $this->raidID = \intval($_REQUEST['id']);
        $this->raid = RaidRuntimeCache::getInstance()->getObject($this->raidID);
        if ($this->raid === null) {
            throw new IllegalLinkException();
        }

        $this->canonicalURL = $this->raid->getLink();
    }
}
