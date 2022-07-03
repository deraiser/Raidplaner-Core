<?php

namespace rp\system\option;

use rp\data\game\Game;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\option\Option;
use wcf\system\option\SelectOptionType;
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
 * Option type implementation for event controller select lists.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Option
 */
class EventControllerSelectOptionType extends SelectOptionType
{

    /**
     * @inheritDoc
     * @return  Game[]
     */
    protected function getSelectOptions(Option $option): array
    {
        $availableEventControllers = ObjectTypeCache::getInstance()->getObjectTypes('info.daries.rp.eventController');

        \uasort($availableEventControllers, function (ObjectType $a, ObjectType $b) {
            return \strcmp(
            WCF::getLanguage()->get('rp.event.controller.' . $a->objectType),
            WCF::getLanguage()->get('rp.event.controller.' . $b->objectType)
            );
        });

        $eventControllers = [];
        /** @var ObjectType $eventController */
        foreach ($availableEventControllers as $eventController) {
            $eventControllers[$eventController->objectType] = WCF::getLanguage()->get('rp.event.controller.' . $eventController->objectType);
        }

        return $eventControllers;
    }
}
