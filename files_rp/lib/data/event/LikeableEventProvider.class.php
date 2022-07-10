<?php

namespace rp\data\event;

use wcf\data\like\ILikeObjectTypeProvider;
use wcf\data\like\object\ILikeObject;
use wcf\data\object\type\AbstractObjectTypeProvider;
use wcf\system\like\IViewableLikeProvider;
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
 * Like Object type provider for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 *
 * @method      LikeableEvent       getObjectByID($objectID)
 * @method      LikeableEvent[]     getObjectsByIDs(array $objectIDs)
 */
class LikeableEventProvider extends AbstractObjectTypeProvider implements
ILikeObjectTypeProvider, IViewableLikeProvider
{
    /**
     * @inheritDoc
     */
    public $className = Event::class;

    /**
     * @inheritDoc
     */
    public $decoratorClassName = LikeableEvent::class;

    /**
     * @inheritDoc
     */
    public $listClassName = EventList::class;

    /**
     * @inheritDoc
     */
    public function checkPermissions(ILikeObject $object): bool
    {
        /** @var LikeableEvent $object */
        return $object->eventID && $object->canRead();
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $likes): void
    {
        $eventIDs = [];
        foreach ($likes as $like) {
            $eventIDs[] = $like->objectID;
        }

        // fetch events
        $eventList = new ViewableEventList();
        $eventList->setObjectIDs($eventIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        // set message
        foreach ($likes as $like) {
            if (isset($events[$like->objectID])) {
                $event = $events[$like->objectID];

                // check permissions
                if (!$event->canRead()) {
                    continue;
                }
                $like->setIsAccessible();

                // short output
                $text = WCF::getLanguage()->getDynamicVariable('wcf.like.title.info.daries.rp.likeableEvent', [
                    'event' => $event,
                    'reaction' => $like,
                ]);
                $like->setTitle($text);
            }
        }
    }
}
