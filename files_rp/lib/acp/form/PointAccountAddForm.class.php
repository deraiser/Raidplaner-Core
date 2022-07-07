<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccountAction;
use rp\data\point\account\PointAccountList;
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
 * Shows the point account add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class PointAccountAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.point.account.add';

    /**
     * @inheritDoc
     */
    public $neededModules = ['RP_ITEM_ACCOUNT_EASYMODE_DISABLED'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManagePointAccount'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = PointAccountAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TextFormField::create('pointAccountName')
            ->label('wcf.global.name')
            ->autoFocus()
            ->required()
            ->maximumLength(255)
            ->i18n()
            ->languageItemPattern('rp.acp.pointAccount.name\d+'),,
            TextFormField::create('description')
            ->label('wcf.global.description')
            ->autoFocus()
            ->maximumLength(255)
            ->i18n()
            ->languageItemPattern('rp.acp.pointAccount.description\d+'),
            ShowOrderFormField::create()
            ->description('rp.acp.point.account.showOrder.description')
            ->required()
            ->options(new PointAccountList()),
        ]);
        $this->form->appendChild($dataContainer);
    }
}
