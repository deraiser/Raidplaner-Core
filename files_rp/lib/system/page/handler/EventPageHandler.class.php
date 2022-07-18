<?php

namespace rp\system\page\handler;

use rp\data\event\ViewableEventList;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;
use wcf\system\page\handler\AbstractLookupPageHandler;
use wcf\system\page\handler\IOnlineLocationPageHandler;
use wcf\system\page\handler\TOnlineLocationPageHandler;
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
 * @author      Marco Daries
 * @package     Daries\RP\System\Page\Handler
 */
class EventPageHandler extends AbstractLookupPageHandler implements IOnlineLocationPageHandler
{
    use TOnlineLocationPageHandler;

    /**
     * @inheritDoc
     */
    public function getLink($objectID): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID)->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getOnlineLocation(Page $page, UserOnline $user): string
    {
        if ($user->pageObjectID === null) {
            return '';
        }

        $event = EventRuntimeCache::getInstance()->getObject($user->pageObjectID);
        if ($event === null || !$event->canRead()) {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, ['event' => $event]);
    }

    /**
     * @inheritDoc
     */
    public function isValid($objectID): bool
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID) !== null;
    }
    /** @noinspection PhpMissingParentCallCommonInspection */

    /**
     * @inheritDoc
     */
    public function isVisible($objectID = null): bool
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($objectID)->canRead();
    }

    /**
     * @inheritDoc
     */
    public function lookup($searchString): array
    {
        $eventList = new ViewableEventList();
        $eventList->getConditionBuilder()->add('event.title LIKE ?', ['%' . $searchString . '%']);
        $eventList->sqlLimit = 10;
        $eventList->sqlOrderBy = 'event.title';
        $eventList->readObjects();

        $results = [];
        foreach ($eventList->getObjects() as $event) {
            $results[] = [
                'description' => $event->getExcerpt(),
                'image' => $event->getUserProfile()->getAvatar()->getImageTag(48),
                'link' => $event->getLink(),
                'objectID' => $event->eventID,
                'title' => $event->getTitle()
            ];
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function prepareOnlineLocation(/** @noinspection PhpUnusedParameterInspection */Page $page, UserOnline $user): void
    {
        if ($user->pageObjectID !== null) {
            ViewableEventRuntimeCache::getInstance()->cacheObjectID($user->pageObjectID);
        }
    }
}
