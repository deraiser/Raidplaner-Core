<?php

namespace rp\acp\form;

use rp\data\character\Character;
use rp\data\character\CharacterAction;
use rp\data\raid\group\RaidGroup;
use rp\data\raid\group\RaidGroupCache;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\MultipleSelectionFormField;
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
 * Shows the assign character to group form.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class CharacterAssignToRaidGroupForm extends AbstractFormBuilderForm
{

    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.list';

    /**
     * ids of the relevant characters
     * @var int[]
     */
    public array $characterIDs = [];

    /**
     * relevant characters
     * @var Character[]
     */
    public $characters = [];

    /**
     * ids of the assigned raid groups
     * @var int[]
     */
    public array $groupIDs = [];

    /**
     * assigned raid groups
     * @var RaidGroup[]
     */
    public array $groups = [];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canEditCharacter'];

    /**
     * id of the character clipboard item object type
     */
    protected ?int $objectTypeID = null;

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'characterIDs' => $this->characterIDs,
            'characters' => $this->characters
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $groupContainer = FormContainer::create('raidGroups')
            ->label('rp.acp.character.raidGroups')
            ->appendChildren([
            MultipleSelectionFormField::create('raidGroupIDs')
            ->options(RaidGroupCache::getInstance()->getGroups())
        ]);
        $this->form->appendChild($groupContainer);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        // get object type id
        $this->objectTypeID = ClipboardHandler::getInstance()->getObjectTypeID('info.daries.rp.character');
        if ($this->objectTypeID === null) {
            throw new SystemException("clipboard item type 'info.daries.rp.character' is unknown.");
        }

        // get characters
        $this->characters = ClipboardHandler::getInstance()->getMarkedItems($this->objectTypeID);
        if (empty($this->characters)) {
            throw new IllegalLinkException();
        }

        $this->characterIDs = \array_keys($this->characters);
    }

    /**
     * @inheritDoc
     */
    public function save()
    {
        AbstractForm::save();

        /** @var MultipleSelectionFormField $raidGroups */
        $raidGroups = $this->form->getNodeById('raidGroupIDs');
        $raidGroupIDs = $raidGroups->getSaveValue();

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add("characterID IN (?)", [$this->characterIDs]);

        $sql = "SELECT  characterID, groupID
                FROM    rp" . WCF_N . "_member_to_raid_group
                " . $conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());
        $groups = $statement->fetchMap('characterID', 'groupID', false);

        $insertErrors = [];
        foreach ($this->characters as $character) {
            $userCharacters = Character::getAllCharactersByUserID($character->userID);
            $userCharacterIDs = \array_keys($userCharacters);

            $groupIDs = [];
            foreach ($raidGroupIDs as $raidGroupID) {
                $raidMemberIDs = RaidGroupCache::getInstance()->getMembersIDsByGroupID($raidGroupID);

                if (\count(\array_intersect($userCharacterIDs, $raidMemberIDs))) {
                    $raidGroup = RaidGroupCache::getInstance()->getGroupByID($raidGroupID);

                    if (!isset($insertErrors[$raidGroupID])) {
                        $insertErrors[$raidGroupID] = [
                            'characters' => [],
                            'raidGroup' => $raidGroup
                        ];
                    }
                    $insertErrors[$raidGroupID]['characters'][] = $character;
                    continue;
                } else {
                    $groupIDs[] = $raidGroupID;
                }
            }

            if (!empty($groupIDs)) {
                $groupsIDs = \array_merge($groups[$character->characterID] ?? [], $groupIDs);
                $groupsIDs = \array_unique($groupsIDs);

                $action = new CharacterAction([$character], 'addToRaidGroups', [
                    'raidGroups' => $groupsIDs
                ]);
                $action->executeAction();
            }
        }

        ClipboardHandler::getInstance()->removeItems($this->objectTypeID);

        AbstractForm::saved();

        WCF::getTPL()->assign([
            'characters' => $this->characters,
            'insertErrors' => $insertErrors,
            'raidGroupIDs' => $raidGroupIDs
        ]);
        WCF::getTPL()->display('characterAssignToRaidGroupSuccess', 'rp');

        exit;
    }
}
