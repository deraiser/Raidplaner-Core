<?php

namespace rp\acp\form;

use rp\data\character\CharacterList;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\search\SearchEditor;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;
use const TIME_NOW;
use rp\system\condition\ICondition;

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
 * Shows the character search form.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class CharacterSearchForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.character.search';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canSearchCharacter'];

    /**
     * list with searched characters
     */
    public CharacterList $characterList;

    /**
     * list of grouped character group assignment condition object types
     * @var ObjectType[][]
     */
    public array $conditions = [];

    /**
     * results per page
     */
    public int $itemsPerPage = 50;

    /**
     * number of results
     */
    public int $maxResults = 2000;

    /**
     * raid group id
     */
    public int $raidGroupID = 0;

    /**
     * search id
     */
    public int $searchID = 0;

    /**
     * sort field
     */
    public string $sortField = 'characterName';

    /**
     * sort order
     */
    public string $sortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public function createForm(): void
    {
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('info.daries.rp.condition.characterSearch');
        foreach ($objectTypes as $objectType) {
            if (!$objectType->conditiongroup) continue;
            if (!isset($this->conditions[$objectType->conditiongroup])) $this->conditions[$objectType->conditiongroup] = [];

            $this->conditions[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
        }

        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterSearchTabMenu');
        $this->form->appendChild($tabMenu);

        foreach ($this->conditions as $conditionGroup => $conditionObjectTypes) {
            $tab = TabFormContainer::create('character_' . $conditionGroup . 'Tab');
            $tab->label('rp.acp.character.condition.conditionGroup.' . $conditionGroup);
            $tabMenu->appendChild($tab);

            $container = FormContainer::create('character_' . $conditionGroup);
            $tab->appendChild($container);

            foreach ($conditionObjectTypes as $condition) {
                $container->appendChild($condition->getProcessor()->getFormField());
                $this->form->getDataHandler()->addProcessor(new VoidFormDataProcessor($condition->getProcessor()->getID()));
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        // search character from passed raidGroupID by raidGroup-view
        if (isset($_GET['raidGroupID'])) {
            $this->raidGroupID = \intval($_GET['raidGroupID']);

            // disable check for security token for GET requests
            $_REQUEST['t'] = WCF::getSession()->getSecurityToken();

            // do search
            try {
                $this->buildForm();
                $this->readData();
                $this->validate();
                $this->save();
            } catch (UserInputException $e) {
                $this->errorField = $e->getField();
                $this->errorType = $e->getType();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function save(): void
    {
        AbstractForm::save();

        // store search result in database
        $data = \serialize([
            'itemsPerPage' => $this->itemsPerPage,
            'matches' => $this->characterList->getObjectIDs(),
        ]);

        $search = SearchEditor::create([
                'searchData' => $data,
                'searchTime' => TIME_NOW,
                'searchType' => 'characters',
                'userID' => WCF::getUser()->userID,
        ]);

        //get new search id
        $this->searchID = $search->searchID;
        $this->saved();

        // forward to result page
        HeaderUtil::redirect(LinkHandler::getInstance()->getLink(
                'CharacterList',
                [
                    'application' => 'rp',
                    'id' => $this->searchID
                ],
                'sortField=' . \rawurlencode($this->sortField) . '&sortOrder=' . \rawurlencode($this->sortOrder)
            )
        );
        exit;
    }

    /**
     * Search for characters which fit to the search values.
     */
    protected function search(): void
    {
        $this->characterList = new CharacterList();
        $this->characterList->sqlLimit = $this->maxResults;

        EventHandler::getInstance()->fireAction($this, 'search');

        // read character ids
        foreach ($this->conditions as $groupedObjectTypes) {
            /** @var ObjectType $objectType */
            foreach ($groupedObjectTypes as $objectType) {
                /** @var ICondition $processor */
                $processor = $objectType->getProcessor();

                if ($this->raidGroupID && $objectType->objectType == 'info.daries.rp.characterRaidGroup') {
                    $processor->setValue([$this->raidGroupID], $this->form);
                }

                $processor->addObjectListCondition($this->characterList, $this->form);
            }
        }
        $this->characterList->readObjectIDs();
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        parent::validate();

        $this->search();

        if (!\count($this->characterList->getObjectIDs())) {
            throw new UserInputException('search', 'noMatches');
        }
    }
}
