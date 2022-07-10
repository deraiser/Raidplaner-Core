<?php

namespace rp\system\event;

use rp\data\event\Event;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\event\EventHandler;
use wcf\system\form\builder\container\IFormContainer;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\BooleanFormField;
use wcf\system\form\builder\field\DateFormField;
use wcf\system\form\builder\field\dependency\EmptyFormFieldDependency;
use wcf\system\form\builder\field\dependency\NonEmptyFormFieldDependency;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\field\validation\FormFieldValidator;
use wcf\system\form\builder\IFormDocument;
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
 * Default implementation for event controllers.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event
 */
abstract class AbstractEventController implements IEventController
{
    /**
     * database object of this event
     */
    protected ?Event $event = null;

    /**
     * Position where Notes should be displayed in this event.
     */
    protected string $eventNodesPosition = '';

    /**
     * object type name
     */
    protected string $objectTypeName = '';

    /**
     * ids of the fields containing object data
     */
    protected array $savedFields = [];

    /**
     * @inheritDoc
     */
    public function createForm(IFormDocument $form): void
    {
        $parameters = [
            'form' => $form
        ];
        EventHandler::getInstance()->fireAction($this, 'createForm', $parameters);
    }

    /**
     * Adds a Boolean form field for enabling comments.
     */
    protected function formComment(IFormContainer $container): void
    {
        $container->appendChild(
            BooleanFormField::create('enableComments')
                ->label('rp.event.enableComments')
                ->value(1)
        );
    }

    /**
     * Adds an event date to the container.
     */
    protected function formEventDate(IFormContainer $container, bool $supportFullDay = false): void
    {
        $time = new \DateTime();
        $time->setTimezone(WCF::getUser()->getTimeZone());

        $isFullDay = BooleanFormField::create('isFullDay')
            ->label('rp.event.isFullDay')
            ->value(0)
            ->available($supportFullDay);

        $container->appendChildren([
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
                        })),
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
                        })),
                DateFormField::create('fStartTime')
                ->label('rp.event.startTime')
                ->required()
                ->value(TIME_NOW)
                ->available($supportFullDay)
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
                ->available($supportFullDay)
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
        ]);

        /** @vor IFormDocument $form */
        $form = $container->getDocument();

        if ($supportFullDay) {
            /** @var DateFormField $startTime */
            $startTime = $form->getNodeById('startTime');
            $startTime->addDependency(
                EmptyFormFieldDependency::create('isFullDay')
                    ->field($isFullDay)
            );

            /** @var DateFormField $endTime */
            $endTime = $form->getNodeById('endTime');
            $endTime->addDependency(
                EmptyFormFieldDependency::create('isFullDay')
                    ->field($isFullDay)
            );
        }

        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('startTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('endTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fStartTime'));
        $form->getDataHandler()->addProcessor(new VoidFormDataProcessor('fEndTime'));

        $form->getDataHandler()->addProcessor(
            new CustomFormDataProcessor(
                'eventDate',
                static function (IFormDocument $document, array $parameters) {
                    $parameters['data']['timezone'] = WCF::getUser()->getTimeZone()->getName();

                    /** @var BooleanFormField $fullDay */
                    $fullDay = $document->getNodeById('isFullDay');
                    /** @var DateFormField $startTime */
                    $startTime = $document->getNodeById($fullDay->getSaveValue() ? 'fStartTime' : 'startTime');
                    /** @var DateFormField $endTime */
                    $endTime = $document->getNodeById($fullDay->getSaveValue() ? 'fEndTime' : 'endTime');

                    $st = $et = null;

                    if ($fullDay->getSaveValue()) {
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
                                new \DateTimeZone('UTC')
                        );

                        $et = \DateTime::createFromFormat(
                                DateFormField::TIME_FORMAT,
                                $endTime->getValue(),
                                new \DateTimeZone('UTC')
                        );

                        $st->setTimezone(WCF::getUser()->getTimeZone());
                        $et->setTimezone(WCF::getUser()->getTimeZone());
                    }

                    $parameters['data']['startTime'] = $st->getTimestamp();
                    $parameters['data']['endTime'] = $et->getTimestamp();

                    return $parameters;
                }
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getEvent(): ?Event
    {
        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function getIcon(?int $size = null): string
    {
        $iconSize = '';
        if ($size) $iconSize = 'icon' . $size;

        return '<span class="icon ' . $iconSize . ' fa-calendar-o"></span>';
    }

    /**
     * @inheritDoc
     */
    public function getObjectTypeName(): string
    {
        return $this->objectTypeName;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function hasLogin(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isExpired(): bool
    {
        return false;
    }

    /**
     * Prepares save of event item.
     */
    protected function prepareSave(array &$formData): void
    {
        EventHandler::getInstance()->fireAction($this, 'prepareSave', $formData);
    }

    /**
     * @inheritDoc
     */
    public function saveForm(array $formData): array
    {
        if (empty($this->savedFields)) return $formData;

        $this->prepareSave($formData);

        $data = [];
        foreach ($this->savedFields as $field) {
            if (isset($formData['data'][$field])) {
                $data[$field] = $formData['data'][$field];
                unset($formData['data'][$field]);
            }
        }

        $data['objectTypeID'] = (ObjectTypeCache::getInstance()->getObjectTypeByName('info.daries.rp.eventController', $this->objectTypeName))->objectTypeID;

        $data['additionalData'] = \serialize($formData['data']);
        unset($formData['data']);

        return \array_merge(['data' => $data], $formData);
    }

    /**
     * @inheritDoc
     */
    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    /**
     * @inheritDoc
     */
    public function setFormObjectData(IFormDocument $form, array $fields = []): void
    {
        $fields = \array_merge(
            [
                'fEndTime' => 'endTime',
                'fStartTime' => 'startTime',
            ],
            $fields,
        );

        EventHandler::getInstance()->fireAction($this, 'beforeSetFormObjectData', $fields);

        foreach ($fields as $key => $value) {
            /** @var AbstractFormField $node */
            $node = $form->getNodeById(\is_string($key) ? $key : $value);
            if ($node) {
                $node->value($this->getEvent()->{$value});
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function showEventNodesPosition(string $position): bool
    {
        return ($this->eventNodesPosition === $position);
    }

    /**
     * Creates a new instance of AbstractEventController.
     */
    public function __construct()
    {
        EventHandler::getInstance()->fireAction($this, '__construct');
    }
}
