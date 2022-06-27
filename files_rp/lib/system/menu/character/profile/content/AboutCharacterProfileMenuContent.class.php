<?php

namespace rp\system\menu\character\profile\content;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\system\event\EventHandler;
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
 * Handles character profile about content.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Menu\Character\Profile\Content
 */
class AboutCharacterProfileMenuContent extends SingletonFactory implements ICharacterProfileMenuContent
{

    /**
     * @inheritDoc
     */
    public function getContent(int $characterID): string
    {
        $character = CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
        
        EventHandler::getInstance()->fireAction($this, 'getContent');

        WCF::getTPL()->assign([
            'notes' => !empty($character->notes) ? $character->getFormattedNotes() : '',
        ]);

        return WCF::getTPL()->fetch('characterProfileAbout', 'rp');
    }

    /**
     * @inheritDoc
     */
    public function isVisible(int $characterID): bool
    {
        return true;
    }
}
