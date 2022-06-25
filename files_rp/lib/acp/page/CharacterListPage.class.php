<?php

namespace rp\acp\page;

use rp\data\character\CharacterList;
use rp\data\character\CharacterProfileList;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\IllegalLinkException;
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
 * Shows a list of characters.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Page
 *
 * @property	CharacterList   $objectList
 */
class CharacterListPage extends SortablePage
{
    /**
     * list of character ids
     * @var int[]
     */
    public array $characterIDs = [];

    /**
     * condition builder for character filtering
     */
    public PreparedStatementConditionBuilder $conditions;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'characterName';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'ASC';

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 50;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canSearchCharacter'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = CharacterProfileList::class;

    /**
     * id of a character search
     */
    public int $searchID = 0;

    /**
     * @inheritDoc
     */
    public $validSortFields = [
        'characterID',
        'characterName',
        'created',
        'rankName',
        'username',
    ];

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('info.daries.rp.character')),
            'searchID' => $this->searchID,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->conditions->add("member.gameID = ?", [RP_DEFAULT_GAME_ID]);

        $this->objectList->sqlSelects = " rank.rankName, user.username";
        $this->objectList->sqlJoins = " LEFT JOIN rp" . WCF_N . "_rank rank ON (rank.rankID = member.rankID)";
        $this->objectList->sqlJoins .= " LEFT JOIN wcf" . WCF_N . "_user user ON (user.userID = member.userID)";

        $this->objectList->setConditionBuilder($this->conditions);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        $this->conditions = new PreparedStatementConditionBuilder();

        if (!empty($_REQUEST['id'])) {
            $this->searchID = \intval($_REQUEST['id']);
            if ($this->searchID) {
                $this->readSearchResult();
            }

            if (empty($this->characterIDs)) {
                throw new IllegalLinkException();
            }

            $this->conditions->add("member.characterID IN (?)", [$this->characterIDs]);
        }
    }

    /**
     * Fetches the result of the search with the given search id.
     */
    protected function readSearchResult(): void
    {
        //get character search from database
        $sql = "SELECT  searchData
                FROM    wcf" . WCF_N . "_search
                WHERE   searchID = ?
                    AND userID = ?
                    AND searchType = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $this->searchID,
            WCF::getUser()->userID,
            'characters'
        ]);
        $search = $statement->fetchArray();
        if (!isset($search['searchData'])) {
            throw new IllegalLinkException();
        }

        $data = \unserialize($search['searchData']);
        $this->characterIDs = $data['matches'];
        $this->itemsPerPage = $data['itemsPerPage'];
        unset($data);
    }

    /**
     * @inheritDoc
     */
    public function show(): void
    {
        $this->activeMenuItem = 'rp.acp.menu.link.character.' . ($this->searchID ? 'search' : 'list');

        parent::show();
    }
}
