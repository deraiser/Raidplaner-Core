<?php

namespace wcf\data\package\installation\plugin;

use rp\data\server\ServerEditor;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
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
 * Installs, updates and deletes servers.
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPServerPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = ServerEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'server';

    /**
     * @inheritDoc
     */
    public $tagName = 'server';

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
            $this->installation->getPackageID(),
        ];

        return [
            'parameters' => $parameters,
            'sql' => $sql,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getDefaultFilename(): string
    {
        return 'rpServer.xml';
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
    protected function prepareImport(array $data): array
    {
        $gameID = null;
        if (!empty($data['elements']['game'])) {
            $sql = "SELECT  gameID
                    FROM    " . $this->application . WCF_N . "_game
                    WHERE   identifier = ?";
            $statement = WCF::getDB()->prepareStatement($sql, 1);
            $statement->execute([$data['elements']['game']]);
            $row = $statement->fetchSingleRow();
            if ($row === false) {
                throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for server '" . $data['attributes']['identifier'] . "'");
            }

            $gameID = $row['gameID'];
        }

        if ($gameID === null) {
            throw new SystemException("The server '" . $data['attributes']['identifier'] . "' must either have an associated game.");
        }

        return [
            'gameID' => $gameID,
            'identifier' => $data['attributes']['identifier'],
            'serverGroup' => $data['elements']['servergroup'] ?? '',
            'type' => $data['elements']['type'],
        ];
    }
}
