<?php

namespace wcf\system\template\plugin;

use rp\util\RPUtil;
use wcf\system\template\TemplateEngine;

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
 * Template modifier plugin which formats a character point.
 *
 * Usage:
 *  {$intOrFloat|characterPoint}
 *  {123456789|characterPoint}
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Template\Plugin
 */
class CharacterPointModifierTemplatePlugin implements IModifierTemplatePlugin
{

    /**
     * @inheritDoc
     */
    public function execute($tagArgs, TemplateEngine $tplObj)
    {
        return RPUtil::formatPoints($tagArgs[0]);
    }
}
