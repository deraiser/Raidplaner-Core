<?php

namespace rp\data\event\legend;

use wcf\data\AbstractDatabaseObjectAction;

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
 * Executes event legend related actions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP
 * @package     Daries\RP\Data\Event\Legend
 * 
 * @method      EventLegendEditor[] getObjects()
 * @method      EventLegendEditor   getSingleObject()
 */
class EventLegendAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = EventLegendEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageEventLegend'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

}
