<?php

namespace rp\system\event;

use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\SingleSelectionFormField;
use wcf\system\form\builder\field\TitleFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\field\wysiwyg\WysiwygFormField;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;
use wcf\util\DateUtil;

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
        $isFullDay = BooleanFormField::create('isFullDay')
            ->label('rp.event.isFullDay')
            ->value(0);

        $dataContainer = FormContainer::create('data')
            ->label('wcf.global.form.data')
            ->appendChildren([
            TitleFormField::create()
            ->required()
            ->maximumLength(255),
            $isFullDay,
            DateFormField::create('startTime')
            ->label('rp.event.startTime')
            ->required()
            ->supportTime()
            ->value(TIME_NOW)
            ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                        $value = $formField->getSaveValue();

                        if ($value === null || $value < -2147483647 || $value > 2147483647) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'invalid',
                                    'rp.event.startTime.error.invalid'
                                )
                            );
                        }
                    }))
            ->addDependency(
                EmptyFormFieldDependency::create('isFullDay')
                ->field($isFullDay)
            ),
            DateFormField::create('endTime')
            ->label('rp.event.endTime')
            ->required()
            ->supportTime()
            ->value(TIME_NOW + 7200) // 2h
            ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                        /** @var DateFormField $startFormField */
                        $startFormField = $formField->getDocument()->getNodeById('startTime');
                        $startValue = $startFormField->getSaveValue();

                        $value = $formField->getSaveValue();

                        if ($value === null || $value <= $startValue || $value > 2147483647) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'invalid',
                                    'rp.event.endTime.error.invalid'
                                )
                            );
                        } else if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'tooLong',
                                    'rp.event.endTime.error.tooLong'
                                )
                            );
                        }
                    }))
            ->addDependency(
                EmptyFormFieldDependency::create('isFullDay')
                ->field($isFullDay)
            ),
            DateFormField::create('fStartTime')
            ->label('rp.event.startTime')
            ->required()
            ->value(TIME_NOW)
            ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                        $value = $formField->getSaveValue();

                        if ($value === null || $value < -2147483647 || $value > 2147483647) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'invalid',
                                    'rp.event.startTime.error.invalid'
                                )
                            );
                        }
                    }))
            ->addDependency(
                NonEmptyFormFieldDependency::create('isFullDay')
                ->field($isFullDay)
            ),
            DateFormField::create('fEndTime')
            ->label('rp.event.endTime')
            ->required()
            ->value(TIME_NOW + 7200) // 2h
            ->addValidator(new FormFieldValidator('uniqueness', function (DateFormField $formField) {
                        /** @var DateFormField $startFormField */
                        $startFormField = $formField->getDocument()->getNodeById('fStartTime');
                        $startValue = $startFormField->getSaveValue();

                        $value = $formField->getSaveValue();

                        if ($value === null || $value < $startValue || $value > 2147483647) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'invalid',
                                    'rp.event.endTime.error.invalid'
                                )
                            );
                        } else if ($value - $startValue > RP_CALENDAR_MAX_EVENT_LENGTH * 86400) {
                            $formField->addValidationError(
                                new FormFieldValidationError(
                                    'tooLong',
                                    'rp.event.endTime.error.tooLong'
                                )
                            );
                        }
                    }))
            ->addDependency(
                NonEmptyFormFieldDependency::create('isFullDay')
                ->field($isFullDay)
            ),
            SingleSelectionFormField::create('timezone')
            ->label('rp.event.timezone')
            ->options(static function () {
                $availableTimezones = [];
                foreach (DateUtil::getAvailableTimezones() as $timezone) {
                    $availableTimezones[$timezone] = WCF::getLanguage()
                        ->get('wcf.date.timezone.' . \str_replace('/', '.', \strtolower($timezone)));
                }
                return $availableTimezones;
            }, false, false)
            ->value(WCF::getUser()->getTimeZone()->getName()),
            WysiwygFormField::create('notes')
            ->label('rp.event.notes')
            ->objectType('info.daries.rp.event.notes')
        ]);
        $form->appendChild($dataContainer);

        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fStartTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fEndTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('timezone'));

        $form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'eventDate',
                static function (IFormDocument $document, array $parameters) {
                    $parameters['data']['timezone'] = WCF::getUser()->getTimeZone()->getName();

                    /** @var BooleanFormField $isFullDay */
                    $isFullDay = $document->getNodeById('isFullDay');
                    /** @var DateFormField $startTime */
                    $startTime = $document->getNodeById($isFullDay->getSaveValue() ? 'fStartTime' : 'startTime');
                    /** @var DateFormField $endTime */
                    $endTime = $document->getNodeById($isFullDay->getSaveValue() ? 'fEndTime' : 'endTime');
                    /** @var SingleSelectionFormField $timezone */
                    $timezone = $document->getNodeById('timezone');

                    $st = $et = null;

                    if ($isFullDay->getSaveValue()) {
                        $st = \DateTime::createFromFormat(
                                DateFormField::DATE_FORMAT,
                                $startTime->getValue(),
                                new \DateTimeZone('UTC')
                        );
                        $st->setTime(0, 0);

                        $et = \DateTime::createFromFormat(
                                DateFormField::DATE_FORMAT,
                                $endTime->getValue(),
                                new \DateTimeZone('UTC')
                        );
                        $et->setTime(23, 59);
                    } else {
                        $st = \DateTime::createFromFormat(
                                DateFormField::TIME_FORMAT,
                                $startTime->getValue(),
                                new \DateTimeZone($timezone->getSaveValue())
                        );

                        $et = \DateTime::createFromFormat(
                                DateFormField::TIME_FORMAT,
                                $endTime->getValue(),
                                new \DateTimeZone($timezone->getSaveValue())
                        );

                        $st->setTimezone(WCF::getUser()->getTimeZone());
                        $et->setTimezone(WCF::getUser()->getTimeZone());
                    }

                    $parameters['data']['endTime'] = $et->getTimestamp();
                    $parameters['data']['startTime'] = $st->getTimestamp();
                    $parameters['data']['timezone'] = $isFullDay->getSaveValue() ? 'UTC' : $timezone->getSaveValue();

                    return $parameters;
                }
            )
        );
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

    /**
     * @inheritDoc
     */
    public function setFormObjectData(IFormDocument $form): void
    {
        /** @var DateFormField $startTime */
        $startTime = $form->getNodeById('fStartTime');
        $startTime->value($this->getEvent()->startTime);

        /** @var DateFormField $endTime */
        $endTime = $form->getNodeById('fEndTime');
        $endTime->value($this->getEvent()->endTime);
    }
}
