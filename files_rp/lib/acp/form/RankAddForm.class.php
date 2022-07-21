<?php

namespace rp\acp\form;

use rp\data\rank\RankAction;
use rp\data\rank\RankCache;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\ShowOrderFormField;
use wcf\system\form\builder\field\TextFormField;

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
 * Shows the rank add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class RankAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.rank.add';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ENABLE_RANK'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRank'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = RankAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TextFormField::create('rankName')
            ->label('wcf.global.name')
            ->required()
            ->autoFocus()
            ->maximumLength(100),
            TextFormField::create('prefix')
            ->label('rp.acp.rank.prefix')
            ->autoFocus()
            ->maximumLength(25),
            TextFormField::create('suffix')
            ->label('rp.acp.rank.suffix')
            ->autoFocus()
            ->maximumLength(25),
            ShowOrderFormField::create()
            ->description('rp.acp.rank.showOrder.description')
            ->required()
            ->options(RankCache::getInstance()->getRanks()),
        ]);
        $this->form->appendChild($dataContainer);
    }
}
