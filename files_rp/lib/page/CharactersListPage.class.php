<?php

namespace rp\page;

use rp\data\character\CharacterProfileList;
use wcf\data\search\Search;
use wcf\page\SortablePage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

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
 * @author      Marco Daries
 * @package     Daries\RP\Page\Page
 *
 * @property    CharacterProfileList    $objectList
 */
class CharactersListPage extends SortablePage
{
    /**
     * available letters
     */
    public static string $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @inheritDoc
     */
    public $defaultSortField = RP_CHARACTERS_LIST_DEFAULT_SORT_FIELD;

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = RP_CHARACTERS_LIST_DEFAULT_SORT_ORDER;

    /**
     * @inheritDoc
     */
    public $itemsPerPage = RP_CHARACTERS_LIST_PER_PAGE;

    /**
     * letter
     */
    public string $letter = '';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canViewCharactersList'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = CharacterProfileList::class;

    /**
     * own characters
     */
    public int $ownCharacters = 0;

    /**
     * character search
     */
    public ?Search $search = null;

    /**
     * id of a character search
     */
    public int $searchID = 0;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['characterName', 'created'];

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'letters' => \str_split(self::$availableLetters),
            'letter' => $this->letter,
            'ownCharacters' => $this->ownCharacters,
            'searchID' => $this->searchID,
            'validSortFields' => $this->validSortFields,
        ]);

        if (\count($this->objectList) === 0) {
            @\header('HTTP/1.1 404 Not Found');
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if ($this->search !== null) {
            $searchData = \unserialize($this->search->searchData);
            $this->objectList->getConditionBuilder()->add("characterID IN (?)", [$searchData['matches']]);
            unset($searchData);
        }

        if (!empty($this->letter)) {
            if ($this->letter == '#') {
                $this->objectList->getConditionBuilder()->add("SUBSTRING(characterName,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
            } else {
                $this->objectList->getConditionBuilder()->add("characterName LIKE ?", [$this->letter . '%']);
            }
        }

        if ($this->ownCharacters) {
            $this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        }

        $this->objectList->getConditionBuilder()->add('gameID = ?', [RP_DEFAULT_GAME_ID]);
        $this->objectList->getConditionBuilder()->add('isDisabled = ?', [0]);
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        // letter
        if (
            isset($_REQUEST['letter']) && \mb_strlen($_REQUEST['letter']) == 1 && \mb_strpos(
                self::$availableLetters,
                $_REQUEST['letter']
            ) !== false
        ) {
            $this->letter = $_REQUEST['letter'];
        }

        if (isset($_REQUEST['ownCharacters'])) {
            $this->ownCharacters = \intval($_REQUEST['ownCharacters']);
        }

        if (!empty($_REQUEST['id'])) {
            $this->searchID = \intval($_REQUEST['id']);
            $this->search = new Search($this->searchID);
            if (!$this->search->searchID || $this->search->userID != WCF::getUser()->userID || $this->search->searchType != 'characters') {
                throw new IllegalLinkException();
            }
        }

        if (!empty($_POST)) {
            $parameters = [
                'application' => 'rp',
            ];
            if ($this->searchID) {
                $parameters['id'] = $this->searchID;
            }
            $url = \http_build_query($_POST, '', '&');
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('CharactersList', $parameters, $url));
            exit;
        }
    }
}
