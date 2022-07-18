<?php

namespace rp\system\page\handler;

use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;
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
 * Implementation of the `IOnlineLocationPageHandler` interface for character-bound pages.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Page\Handler
 */
trait TCharacterOnlineLocationPageHandler
{
    use TOnlineLocationPageHandler;

    /**
     * Returns the textual description if a user is currently online viewing this page.
     *
     * @see IOnlineLocationPageHandler::getOnlineLocation()
     */
    public function getOnlineLocation(Page $page, UserOnline $user): string
    {
        if ($user->pageObjectID === null) {
            return '';
        }

        $charcterObject = CharacterRuntimeCache::getInstance()->getObject($user->pageObjectID);
        if ($charcterObject === null) {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('wcf.page.onlineLocation.' . $page->identifier, [
                'character' => $charcterObject,
                'userOnline' => $user,
        ]);
    }

    /**
     * Prepares fetching all necessary data for the textual description if a user is currently online
     * viewing this page.
     *
     * @see IOnlineLocationPageHandler::prepareOnlineLocation()
     */
    public function prepareOnlineLocation(Page $page, UserOnline $user): void
    {
        if ($user->pageObjectID !== null) {
            CharacterRuntimeCache::getInstance()->cacheObjectID($user->pageObjectID);
        }
    }
}
