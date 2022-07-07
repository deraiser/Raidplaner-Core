<?php

namespace rp\system\clipboard\action;

use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
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
 * Clipboard action implementation for raid attendees.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\System\Clipboard\Action
 */
class EventRaidAttendeeClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = [
        'delete'
    ];

    /**
     * @inheritDoc
     */
    protected $supportedActions = [
        'updateStatus',
        'delete'
    ];

    /**
     * @inheritDoc
     */
    public function execute($objects, ClipboardAction $action): ?ClipboardEditorItem
    {
        $item = parent::execute($objects, $action);

        if ($item === null) {
            return null;
        }

        // handle actions
        switch ($action->actionName) {
            case 'updateStatus':
                $item->addInternalData('template', WCF::getTPL()->fetch('eventRaidAttendeeStatusDialog', 'rp', [
                        'statusData' => [
                            EventRaidAttendee::STATUS_CONFIRMED => WCF::getLanguage()->get('rp.event.raid.container.confirmed'),
                            EventRaidAttendee::STATUS_LOGIN => WCF::getLanguage()->get('rp.event.raid.container.login'),
                            EventRaidAttendee::STATUS_RESERVE => WCF::getLanguage()->get('rp.event.raid.container.reserve'),
                            EventRaidAttendee::STATUS_LOGOUT => WCF::getLanguage()->get('rp.event.raid.container.logout'),
                        ]
                ]));
                $item->addInternalData('objectIDs', $item->getParameters()['objectIDs']);
                break;

            case 'delete':
                $item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.info.daries.rp.raid.attendee.delete.confirmMessage', [
                        'count' => $item->getCount()
                ]));
                break;
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return EventRaidAttendeeAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return 'info.daries.rp.raid.attendee';
    }

    /**
     * Returns the ids of the participants who have access to the event.
     * 
     * @return	int[]
     */
    public function validateUpdateStatus(): array
    {
        $objectIDs = [];

        /** @var EventRaidAttendee $attendee */
        foreach ($this->objects as $attendee) {
            if ($attendee->getEvent()->canEdit()) {
                $objectIDs[] = $attendee->attendeeID;
            }
        }

        return $objectIDs;
    }

    /**
     * Returns the ids of the participants who have access to the event.
     * 
     * @return	int[]
     */
    public function validateDelete(): array
    {
        $objectIDs = [];

        /** @var EventRaidAttendee $attendee */
        foreach ($this->objects as $attendee) {
            if ($attendee->getEvent()->canEdit()) {
                $objectIDs[] = $attendee->attendeeID;
            }
        }

        return $objectIDs;
    }
}
