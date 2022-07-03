<?php

namespace rp\system;

use rp\page\CalendarPage;
use rp\system\character\CharacterHandler;
use rp\system\menu\character\profile\CharacterProfileMenu;
use wcf\system\application\AbstractApplication;
use wcf\system\request\route\StaticRequestRoute;
use wcf\system\request\RouteHandler;

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
 * This class extends the main WCF class by raidplaner specific functions.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System
 */
class RPCore extends AbstractApplication
{
    /**
     * @inheritDoc
     */
    protected $primaryController = CalendarPage::class;

    /**
     * @inheritDoc
     */
    public function __run(): void
    {
        $route = new StaticRequestRoute();
        $route->setStaticController('rp', 'Calendar');
        $route->setBuildSchema('/{controller}/{year}/{month}/');
        $route->setPattern('~^/?(?P<controller>[^/]+)/(?P<year>\d{4})(?:/(?P<month>\d{1,2}))?~x');
        $route->setRequiredComponents(['year' => '~^\d{4}$~']);
        $route->setMatchController(true);
        RouteHandler::getInstance()->addRoute($route);
    }

    /**
     * Returns the character handler of the current user
     */
    public function getCharacter(): CharacterHandler
    {
        return CharacterHandler::getInstance();
    }

    /**
     * Returns the character profile menu
     */
    public function getCharacterProfileMenu(): CharacterProfileMenu
    {
        return CharacterProfileMenu::getInstance();
    }
}
