<?php

use rp\data\game\GameEditor;
use wcf\data\option\OptionEditor;
use wcf\system\exception\SystemException;
use wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin;
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
 * Installs, updates and deletes games.
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPGamePackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'game';

    /**
     * @inheritDoc
     */
    public $tagName = 'game';

    /**
     * @inheritDoc
     */
    protected function findExistingItem(array $data): array
    {
        $sql = "SELECT	*
                FROM	" . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE	identifier = ?
                    AND packageID = ?";
        $parameters = [
            $data['identifier'],
            $this->installation->getPackageID()
        ];

        return [
            'sql' => $sql,
            'parameters' => $parameters,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpGame.xml';
    }

    /**
     * @inheritDoc
     */
    public static function getSyncDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    protected function handleDelete(array $items): void
    {
        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "
                WHERE       identifier = ?
                    AND     packageID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($items as $item) {
            $statement->execute([
                $item['attributes']['identifier'],
                $this->installation->getPackageID(),
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * @inheritDoc
     */
    protected function postImport(): void
    {
        // set default game
        if (!\defined('RP_DEFAULT_GAME_ID') || !RP_DEFAULT_GAME_ID) {
            $sql = "SELECT  gameID
                    FROM    rp" . WCF_N . "_game";
            $statement = WCF::getDB()->prepareStatement($sql, 1);
            $statement->execute();
            $gameID = $statement->fetchSingleColumn();

            if ($gameID !== false) {
                $sql = "UPDATE  wcf" . WCF_N . "_option
                        SET     optionValue = ?
                        WHERE   optionName = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $gameID,
                    'rp_default_game_id',
                ]);

                // update options.inc.php
                OptionEditor::resetCache();
            }
        }
    }

    /**
     * @inheritDoc
     */
    protected function prepareImport(array $data): array
    {
        return [
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
            'maxLevel' => $data['elements']['maxLevel'],
        ];
    }
}
