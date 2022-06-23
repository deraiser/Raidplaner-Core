<?php

namespace rp\data\character;

use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use wcf\data\IPopoverAction;
use wcf\system\exception\UserInputException;
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
 * Executes character profile-related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 */
class CharacterProfileAction extends CharacterAction implements IPopoverAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getPopover'];

    /**
     * @inheritDoc
     */
    public function getPopover(): array
    {
        $characterID = \reset($this->objectIDs);

        if ($characterID) {
            $characterProfile = CharacterProfileRuntimeCache::getInstance()->getObject($characterID);
            if ($characterProfile) {
                WCF::getTPL()->assign('character', $characterProfile);
            } else {
                WCF::getTPL()->assign('unknownCharacter', true);
            }
        } else {
            WCF::getTPL()->assign('unknownCharacter', true);
        }

        return [
            'template' => WCF::getTPL()->fetch('characterProfilePreview', 'rp'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function validateGetPopover(): void
    {
        WCF::getSession()->checkPermissions(['user.rp.canViewCharacterProfile']);

        if (\count($this->objectIDs) != 1) {
            throw new UserInputException('objectIDs');
        }
    }
}
