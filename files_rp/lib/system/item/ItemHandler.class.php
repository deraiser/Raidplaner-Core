<?php

namespace rp\system\item;

use rp\data\item\database\ItemDatabase;
use rp\data\item\database\ItemDatabaseList;
use rp\data\item\Item;
use rp\data\item\ItemAction;
use rp\data\item\ItemCache;
use wcf\data\user\User;
use wcf\system\language\LanguageFactory;
use wcf\system\session\SessionHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

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
 * Handles items.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Item
 */
class ItemHandler extends SingletonFactory
{
    /**
     * item database list
     */
    protected ?ItemDatabaseList $databases = null;

    /**
     * Returns an item based on the item name
     */
    final public function getSearchItem(string $itemName, int $itemID = 0, bool $refresh = false, array $data = []): Item
    {
        $itemName = StringUtil::trim($itemName);
        if (empty($itemName) && !$itemID) return null;

        $item = $searchItemID = null;
        if ($itemID) {
            $item = ItemCache::getInstance()->getItemByID($itemID);
            if ($item) $searchItemID = $item->searchItemID;
        } else {
            $item = ItemCache::getInstance()->getItemByName($itemName);
            if ($item) $searchItemID = $item->searchItemID;
        }

        if ($item === null || $refresh) {
            $newItem = null;
            $user = WCF::getUser();
            try {
                SessionHandler::getInstance()->changeUser(new User(null), true);
                if (!WCF::debugModeIsEnabled()) {
                    \ob_start();
                }

                if ($this->databases !== null) {
                    /** @var ItemDatabase $database */
                    foreach ($this->databases as $database) {
                        $parser = new $database->className();

                        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                            $searchData = [];

                            if ($searchItemID === null || empty($searchItemID)) {
                                $searchData = $parser->searchItemID($itemName, $language);
                            } else {
                                $searchData = [
                                    $searchItemID,
                                    $data['type'] ?? 'items'
                                ];
                            }

                            try {
                                $newItem = $parser->getItemData(
                                    $searchData[0],
                                    $language,
                                    $searchData[1]
                                );
                            } catch (\Exception $e) {
                                $newItem == null;
                            }

                            if ($newItem !== null) break;
                        }

                        if ($newItem !== null) break;
                    }
                }
            } catch (\Exception $e) {
                $newItem == null;
            } finally {
                if (!WCF::debugModeIsEnabled()) {
                    \ob_end_clean();
                }
                SessionHandler::getInstance()->changeUser($user, true);
            }

            $saveSearchItemID = '';
            if ($newItem !== null) {
                $saveSearchItemID = $newItem['id'];
                unset($newItem['id']);
            } else {
                $newItem = [];
            }

            if ($item) {
                $action = new ItemAction([$item], 'update', ['data' => [
                        'searchItemID' => $saveSearchItemID,
                        'additionalData' => \serialize($newItem)
                ]]);
                $action->executeAction();

                // reload item
                $item = new Item($item->itemID);
            } else {
                $action = new ItemAction([], 'create', ['data' => [
                        'itemName' => $itemName,
                        'searchItemID' => $saveSearchItemID,
                        'additionalData' => \serialize($newItem)
                ]]);
                $item = $action->executeAction()['returnValues'];
            }
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        if (!empty(RP_ITEM_DATABASES)) {
            $list = new ItemDatabaseList();
            $list->getConditionBuilder()->add('databaseName IN (?)', [\explode(',', RP_ITEM_DATABASES)]);
            $list->readObjects();
            $this->databases = $list;
        }
    }
}
