<?php

namespace rp\data\raid\group;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\package\PackageCache;
use wcf\system\language\I18nHandler;

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
 * Executes raid group-related actions.
 *
 * @author      Marco Daries
 * @package     Daries\RP\Data\Raid\Group
 *
 * @method      RaidGroupEditor[]   getObjects()
 * @method      RaidGroupEditor     getSingleObject()
 */
class RaidGroupAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public $className = RaidGroupEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageRaidGroup'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageRaidGroup'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageRaidGroup'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    /**
     * @inheritDoc
     */
    public function create(): RaidGroup
    {
        // The groupName cannot be empty by design, but cannot be filled proper if the
        // multilingualism is enabled, therefore, we must fill the tilte with a dummy value.
        if (!isset($this->parameters['data']['groupName']) && isset($this->parameters['groupName_i18n'])) {
            $this->parameters['data']['groupName'] = 'wcf.global.name';
        }

        /** @var RaidGroup $raidGroup */
        $raidGroup = parent::create();

        $updateData = [];

        // i18n
        if (isset($this->parameters['groupName_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['groupName_i18n'],
                'rp.raid.group.name' . $raidGroup->groupID,
                'rp.raid.group',
                PackageCache::getInstance()->getPackageID('info.daries.rp')
            );

            $updateData['groupName'] = 'rp.raid.group.name' . $raidGroup->groupID;
        }

        if (isset($this->parameters['groupDescription_i18n'])) {
            I18nHandler::getInstance()->save(
                $this->parameters['groupDescription_i18n'],
                'rp.raid.group.description' . $raidGroup->groupID,
                'rp.raid.group',
                PackageCache::getInstance()->getPackageID('info.daries.rp')
            );

            $updateData['groupDescription'] = 'rp.raid.group.description' . $raidGroup->groupID;
        }

        if (!empty($updateData)) {
            $raidGroupEditor = new RaidGroupEditor($raidGroup);
            $raidGroupEditor->update($updateData);
        }

        return $raidGroup;
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        parent::update();

        foreach ($this->getObjects() as $object) {
            $updateData = [];

            if (isset($this->parameters['groupName_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['groupName_i18n'],
                    'rp.raid.group.name' . $object->groupID,
                    'rp.raid.group',
                    PackageCache::getInstance()->getPackageID('info.daries.rp')
                );

                $updateData['groupName'] = 'rp.raid.group.name' . $object->groupID;
            }

            if (isset($this->parameters['groupDescription_i18n'])) {
                I18nHandler::getInstance()->save(
                    $this->parameters['groupDescription_i18n'],
                    'rp.raid.group.description' . $object->groupID,
                    'rp.raid.group',
                    PackageCache::getInstance()->getPackageID('info.daries.rp')
                );

                $updateData['groupDescription'] = 'rp.raid.group.description' . $object->groupID;
            }

            if (!empty($updateData)) {
                $object->update($updateData);
            }
        }
    }
}
