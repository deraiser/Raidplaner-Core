<?php

namespace rp\system\box;

use rp\data\event\FilteredEventList;
use wcf\system\box\AbstractDatabaseObjectListBoxController;
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
 * Database object list controller implementation for a list of events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Box
 */
class EventListBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    /**
     * list of sort fields that require joining the `rp1_event` table
     * @var string[]
     */
    public array $eventSortFields = [
        'startTime',
        'views',
    ];

    /**
     * @inheritDoc
     */
    protected $sortFieldLanguageItemPrefix = 'rp.event.sortField';


    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'startTime',
        'comments',
        'views',
    ];

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        $filteredEventList = new FilteredEventList(TIME_NOW, TIME_NOW + 86400 * 365 * 2);
        $filteredEventList->getConditionBuilder()->add('isCanceled = ?', [0]);
        return $filteredEventList;
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        $templateName = 'boxEventList';
        if ($this->box->position === 'sidebarLeft' || $this->box->position === 'sidebarRight') {
            $templateName = 'boxEventListSidebar';
        }

        return WCF::getTPL()->fetch($templateName, 'rp', [
                'boxEventList' => $this->objectList,
                'boxSortField' => $this->sortField,
                ], true);
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        if (!WCF::getSession()->getPermission('user.rp.canReadEvent')) {
            return false;
        }

        return parent::hasContent();
    }
}
