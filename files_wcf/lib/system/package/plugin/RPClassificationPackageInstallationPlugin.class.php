<?php

namespace wcf\system\package\plugin;

use rp\data\classification\ClassificationEditor;
use rp\data\faction\Faction;
use rp\data\race\Race;
use rp\data\role\Role;
use wcf\data\IStorableObject;
use wcf\system\devtools\pip\IIdempotentPackageInstallationPlugin;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

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
 * Installs, updates and deletes classifications.
 * 
 * @author      Marco Daries
 * @package     WoltLabSuite\Core\System\Package\Plugin
 */
class RPClassificationPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin implements IIdempotentPackageInstallationPlugin
{
    /**
     * @inheritDoc
     */
    public $application = 'rp';

    /**
     * @inheritDoc
     */
    public $className = ClassificationEditor::class;

    /**
     * @inheritDoc
     */
    public $tableName = 'classification';

    /**
     * @inheritDoc
     */
    public $tagName = 'classification';

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
        return 'rpClassification.xml';
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
    protected function import(array $row, array $data): IStorableObject
    {
        $factions = $data['factions'];
        unset($data['factions']);
        $races = $data['races'];
        unset($data['races']);
        $roles = $data['roles'];
        unset($data['roles']);

        $classification = parent::import($row, $data);

        // delete old entry
        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "_to_faction
                WHERE       classificationID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$classification->classificationID]);

        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "_to_race
                WHERE       classificationID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$classification->classificationID]);

        $sql = "DELETE FROM " . $this->application . WCF_N . "_" . $this->tableName . "_to_role
                WHERE       classificationID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$classification->classificationID]);

        // new entry to factions
        $sql = "INSERT INTO " . $this->application . WCF_N . "_" . $this->tableName . "_to_faction
                            (classificationID, factionID)
                VALUES      (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($factions as $faction) {
            $statement->execute([
                $classification->classificationID,
                $faction->factionID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        // new entry to races
        $sql = "INSERT INTO " . $this->application . WCF_N . "_" . $this->tableName . "_to_race
                            (classificationID, raceID)
                VALUES      (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($races as $race) {
            $statement->execute([
                $classification->classificationID,
                $race->raceID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        // new entry to roles
        $sql = "INSERT INTO " . $this->application . WCF_N . "_" . $this->tableName . "_to_role
                            (classificationID, roleID)
                VALUES      (?, ?)";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();
        foreach ($roles as $role) {
            $statement->execute([
                $classification->classificationID,
                $role->roleID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        return $classification;
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
                throw new SystemException("Unable to find game '" . $data['elements']['game'] . "' for classification '" . $data['attributes']['identifier'] . "'");
            }

            $gameID = $row['gameID'];
        }

        if ($gameID === null) {
            throw new SystemException("The classification '" . $data['attributes']['identifier'] . "' must either have an associated game.");
        }

        $factions = [];
        if (isset($data['elements']['factions'])) {
            $tmpFactions = ArrayUtil::trim(\explode(',', $data['elements']['factions']));
            foreach ($tmpFactions as $factionName) {
                $sql = "SELECT  factionID
                        FROM    " . $this->application . WCF_N . "_faction
                        WHERE   identifier = ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql, 1);
                $statement->execute([
                    $factionName,
                    $gameID,
                ]);
                $row = $statement->fetchSingleRow();
                if ($row === false) {
                    throw new SystemException("Unable to find faction '" . $factionName . "' for classification '" . $data['attributes']['identifier'] . "'");
                }

                $factions[] = new Faction(null, $row);
            }
        }

        $races = [];
        if (isset($data['elements']['races'])) {
            $tmpRaces = ArrayUtil::trim(\explode(',', $data['elements']['races']));
            foreach ($tmpRaces as $raceName) {
                $sql = "SELECT  raceID
                        FROM    " . $this->application . WCF_N . "_race
                        WHERE   identifier = ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql, 1);
                $statement->execute([
                    $raceName,
                    $gameID,
                ]);
                $row = $statement->fetchSingleRow();
                if ($row === false) {
                    throw new SystemException("Unable to find race '" . $raceName . "' for classification '" . $data['attributes']['identifier'] . "'");
                }

                $races[] = new Race(null, $row);
            }
        }

        $roles = [];
        if (isset($data['elements']['roles'])) {
            $tmpRoles = ArrayUtil::trim(\explode(',', $data['elements']['roles']));
            foreach ($tmpRoles as $roleName) {
                $sql = "SELECT  roleID
                        FROM    " . $this->application . WCF_N . "_role
                        WHERE   identifier = ?
                            AND gameID = ?";
                $statement = WCF::getDB()->prepareStatement($sql, 1);
                $statement->execute([
                    $roleName,
                    $gameID
                ]);
                $row = $statement->fetchSingleRow();
                if ($row === false) {
                    throw new SystemException("Unable to find role '" . $roleName . "' for classification '" . $data['attributes']['identifier'] . "'");
                }

                $roles[] = new Role(null, $row);
            }
        }

        return [
            'factions' => $factions,
            'gameID' => $gameID,
            'icon' => $data['elements']['icon'] ?? '',
            'identifier' => $data['attributes']['identifier'],
            'races' => $races,
            'roles' => $roles,
        ];
    }
}
