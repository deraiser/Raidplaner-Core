<?php

namespace rp\system\event;

use rp\system\form\builder\field\DateFormField;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;

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
 * Default event implementation for event controllers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
class DefaultEventController extends AbstractEventController
{
    /**
     * @inheritDoc
     */
    protected string $objectTypeName = 'info.daries.rp.event.default';

    /**
     * @inheritDoc
     */
    protected array $savedFields = [
        'endTime',
        'isFullDay',
        'notes',
        'startTime',
        'title',
        'userID',
        'username'
    ];

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChild(
            TitleFormField::create()
            ->required()
            ->maximumLength(255)
        );
        $form->appendChild($dataContainer);

        $this->formEventDate($dataContainer, true);

        $dataContainer->appendChild(
            WysiwygFormField::create('notes')
                ->label('rp.event.notes')
                ->objectType('info.daries.rp.event.notes')
        );

        parent::createForm($form);
    }

    /**
     * @inheritDoc
     */
    public function getContent(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getModerationTemplate(): string
    {
        return 'moderationEventDefault';
    }
}
