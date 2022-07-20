<?php

namespace rp\acp\form;

use rp\data\point\account\PointAccountList;
use rp\data\raid\event\RaidEventAction;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\dependency\ValueFormFieldDependency;
use wcf\system\form\builder\field\FloatFormField;
use wcf\system\form\builder\field\RadioButtonFormField;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TextFormField;
use wcf\system\form\builder\field\UploadFormField;
use wcf\system\Regex;
use wcf\util\DirectoryUtil;

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
 * Shows the raid event add form.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Acp\Form
 */
class RaidEventAddForm extends AbstractFormBuilderForm
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'rp.acp.menu.link.raid.event.add';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.rp.canManageRaidEvent'];

    /**
     * @inheritDoc
     */
    public $objectActionClass = RaidEventAction::class;

    /**
     * @inheritDoc
     */
    protected function createForm(): void
    {
        parent::createForm();

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TextFormField::create('eventName')
            ->label('wcf.global.name')
            ->autoFocus()
            ->required()
            ->maximumLength(255)
            ->i18n()
            ->languageItemPattern('rp.raid.event.name\d+'),
            SingleSelectionFormField::create('pointAccountID')
            ->label('rp.acp.raid.event.point.account')
            ->options(new PointAccountList()),
            FloatFormField::create('defaultPoints')
            ->label('rp.acp.raid.event.defaultPoints')
            ->description('rp.acp.raid.event.defaultPoints.description')
            ->value(0),
            BooleanFormField::create('showProfile')
            ->label('rp.acp.raid.event.showProfile')
            ->value(1)
        ]);

        $iconContainer = FormContainer::create('imageSection')
            ->label('rp.acp.raid.event.image')
            ->appendChildren([
            RadioButtonFormField::create('imageMode')
            ->label('rp.acp.raid.event.image.mode')
            ->options([
                'select' => 'rp.acp.raid.event.image.mode.select',
                'upload' => 'rp.acp.raid.event.image.mode.upload'
            ])
            ->available($this->formAction === null)
            ->value('select'),
            RadioButtonFormField::create('icon')
            ->label('rp.acp.raid.event.image.mode.select')
            ->required()
            ->options(function () {
                $fileNameRegex = new Regex('^[^?]*\.png');
                $files = DirectoryUtil::getInstance(RP_DIR . 'images/raid/event/')->getFiles(SORT_DESC, $fileNameRegex);

                $options = [];
                foreach (\array_flip(\array_map('basename', $files)) as $filename => $fullname) {
                    $ext = \explode('.', $filename);
                    \array_pop($ext);
                    $options[\implode($ext)] = '<img src="' . RELATIVE_RP_DIR . 'images/raid/event/' . $filename . '" alt="" style="width: 25px; height: 25px"> ' . $filename;
                }

                return $options;
            }, false, false)
            ->required()
            ->addClass('floated'),
            UploadFormField::create('iconFile')
            ->label('rp.acp.raid.event.image.mode.upload')
            ->required()
            ->maximum(1)
            ->imageOnly(true)
            ->allowSvgImage(true)
            ->available($this->formAction === null)
        ]);

        $this->form->appendChildren([
            $dataContainer,
            $iconContainer
        ]);

        // `imageMode` is an internal field not meant to be
        // treated as real data
        $this->form->getDataHandler()->addProcessor(new VoidFormDataProcessor('imageMode'));

        /** @var RadioButtonFormField $modeField */
        $modeField = $iconContainer->getNodeById('imageMode');

        $iconContainer->getNodeById('icon')->addDependency(
            ValueFormFieldDependency::create('modeSelect')
                ->field($modeField)
                ->values(['select'])
        );

        $iconContainer->getNodeById('iconFile')->addDependency(
            ValueFormFieldDependency::create('modeUpload')
                ->field($modeField)
                ->values(['upload'])
        );
    }
}
