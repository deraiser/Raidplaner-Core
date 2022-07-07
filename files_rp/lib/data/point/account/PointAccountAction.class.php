<?php

namespace rp\data\point\account;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\data\language\item\LanguageItemAction;
use wcf\data\package\PackageCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
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
 * Executes point account related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Point\Account
 * 
 * @method      PointAccountEditor[]    getObjects()
 * @method      PointAccountEditor      getSingleObject()
 */
class PointAccountAction extends AbstractDatabaseObjectAction implements ISortableAction
{
    /**
     * @inheritDoc
     */
    protected $className = PointAccountEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update', 'updatePosition'];

    /**
     * @inheritDoc
     */
    public function create(): PointAccount
    {
        if (!isset($this->parameters['data']['gameID'])) {
            $this->parameters['data']['gameID'] = RP_DEFAULT_GAME_ID;
        }
        
        $showOrder = 0;
        if (isset($this->parameters['data']['showOrder'])) {
            $showOrder = $this->parameters['data']['showOrder'];
            unset($this->parameters['data']['showOrder']);
        }

        // The point account name cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the point account name with a dummy value.
        if (!isset($this->parameters['data']['pointAccountName']) && isset($this->parameters['pointAccountName_i18n'])) {
            $this->parameters['data']['pointAccountName'] = 'wcf.global.name';
        }

        // The description cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the description with a dummy value.
        if (!isset($this->parameters['data']['description']) && isset($this->parameters['description_i18n'])) {
            $this->parameters['data']['description'] = 'wcf.global.description';
        }

        /** @var PointAccount $pointAccount */
        $pointAccount = parent::create();
        $pointAccountEditor = new PointAccountEditor($pointAccount);

        $pointAccountEditor->setShowOrder($showOrder);

        // i18n
        $updateData = [];
        if (isset($this->parameters['pointAccountName_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['pointAccountName_i18n'],
                'rp.acp.pointAccount.name' . $pointAccountEditor->pointAccountID,
                'rp.acp.pointAccount',
                PackageCache::getInstance()->getPackageID('info.daries.rp')
            );

            $updateData['pointAccountName'] = 'rp.acp.pointAccount.name' . $pointAccountEditor->pointAccountID;
        }
        if (isset($this->parameters['description_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['description_i18n'],
                'rp.acp.pointAccount.description' . $pointAccountEditor->pointAccountID,
                'rp.acp.pointAccount',
                PackageCache::getInstance()->getPackageID('info.daries.rp')
            );

            $updateData['description'] = 'rp.acp.pointAccount.description' . $pointAccountEditor->pointAccountID;
        }

        if (!empty($updateData)) {
            $reactionTypeEditor->update($updateData);
        }

        return $label;
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        parent::delete();

        if (!empty($this->objects)) {
            // identify i18n labels
            $languageVariables = [];
            foreach ($this->getObjects() as $object) {
                if (\preg_match('~rp.point.account.name\d+~', $object->pointAccountName)) {
                    $languageVariables[] = $object->pointAccountName;
                }
                if (\preg_match('~rp.point.account.description\d+~', $object->description)) {
                    $languageVariables[] = $object->description;
                }
            }

            // remove language variables
            if (!empty($languageVariables)) {
                $conditions = new PreparedStatementConditionBuilder();
                $conditions->add("languageItem IN (?)", [$languageVariables]);

                $sql = "SELECT  languageItemID
                        FROM    wcf" . WCF_N . "_language_item
                        " . $conditions;
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute($conditions->getParameters());
                $languageItemIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);

                $objectAction = new LanguageItemAction($languageItemIDs, 'delete');
                $objectAction->executeAction();
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        parent::update();

        // update showOrder if required
        if (
            \count($this->objects) === 1 &&
            isset($this->parameters['data']['showOrder'])
        ) {
            $pointAccountEditor = $this->getObjects()[0];
            if ($pointAccountEditor->showOrder != $this->parameters['data']['showOrder']) {
                $pointAccountEditor->setShowOrder(
                    $this->parameters['data']['showOrder']
                );
            }
        }

        foreach ($this->getObjects() as $object) {
            $updateData = [];

            // i18n
            if (isset($this->parameters['pointAccountName_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['pointAccountName_i18n'],
                    'rp.acp.pointAccount.name' . $object->pointAccountID,
                    'rp.acp.pointAccount',
                    PackageCache::getInstance()->getPackageID('info.daries.rp')
                );

                $updateData['pointAccountName'] = 'rp.acp.pointAccount.name' . $object->pointAccountID;
            }

            if (isset($this->parameters['description_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['description_i18n'],
                    'rp.acp.pointAccount.description' . $object->pointAccountID,
                    'rp.acp.pointAccount',
                    PackageCache::getInstance()->getPackageID('info.daries.rp')
                );

                $updateData['description'] = 'rp.acp.pointAccount.description' . $object->pointAccountID;
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updatePosition(): void
    {
        $sql = "UPDATE  rp" . WCF_N . "_point_account
                SET     showOrder = ?
                WHERE   pointAccountID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        $showOrder = $this->parameters['data']['offset'];

        WCF::getDB()->beginTransaction();
        foreach ($this->parameters['data']['structure'] as $pointAccountID) {
            $statement->execute([
                $showOrder++,
                $pointAccountID,
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    public function validateUpdatePosition(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canManagePointAccount']);

        if (!isset($this->parameters['data']) || !isset($this->parameters['data']['structure']) || !\is_array($this->parameters['data']['structure'])) {
            throw new UserInputException('structure');
        }

        $this->readInteger('offset', true, 'data');
    }
}
