<?php

namespace rp\data\character;

use rp\system\character\CharacterHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\data\TDatabaseObjectToggle;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;

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
 * Executes character related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Character
 * 
 * @method      CharacterEditor[]   getObjects()
 * @method      CharacterEditor     getSingleObject()
 */
class CharacterAction extends AbstractDatabaseObjectAction implements IToggleAction
{
    use TDatabaseObjectToggle;
    /**
     * @inheritDoc
     */
    protected $className = CharacterEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canDeleteCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
    public function create(): Character
    {
        $this->parameters['data']['created'] = $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if ($this->parameters['data']['userID'] !== null) {
            if (RequestHandler::getInstance()->isACPRequest()) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('userID = ?', [$this->parameters['data']['userID']]);
                $characterList->getConditionBuilder()->add('isPrimary = ?', [1]);
                $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
                $this->parameters['data']['isPrimary'] = \intval(($characterList->countObjects() === 0));
            } else {
                $this->parameters['data']['isPrimary'] = \intval((CharacterHandler::getInstance()->getPrimaryCharacter() === null));
            }
        } else {
            $this->parameters['data']['isDisabled'] = 1;
        }

        /** @var Character $character */
        $character = parent::create();

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }

    /**
     * @inheritDoc
     */
    public function delete()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $character) {
            if ($character->userID) {
                UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
            }
        }

        return parent::delete();
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        if (isset($this->parameters['data']) || isset($this->parameters['counters'])) {
            if ($this->parameters['data']['userID'] === null) {
                $this->parameters['data']['isDisabled'] = 1;
            }

            parent::update();
        } else {
            if (empty($this->objects)) {
                $this->readObjects();
            }
        }
    }
}
