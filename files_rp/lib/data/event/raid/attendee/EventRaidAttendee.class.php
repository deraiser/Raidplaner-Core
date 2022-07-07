<?php

namespace rp\data\event\raid\attendee;

use rp\data\character\CharacterProfile;
use rp\data\event\Event;
use rp\data\role\Role;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\cache\runtime\EventRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
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
 * Represents a event raid attendee.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Event\Raid\Attendee
 * 
 * @property-read   int     $attendeeID             unique id of the attendee
 * @property-read   int     $eventID                id of the event
 * @property-read   int     $characterID            id of the character
 * @property-read   int     $characterName          character name
 * @property-read   string  $email                  email address of the participant for a guest registration.
 * @property-read   int     $classificationID       id of the classification
 * @property-read   int     $roleID                 id of the role
 * @property-read   string  $notes                  notes of the attendee
 * @property-read   int     $created                timestamp at which the attendee has been created
 * @property-read   int     $addByLeader            is `1` if the attendee added by raid leader, otherwise `0`
 * @property-read   int     $status                 status of the raid attendee (see `EventRaidAttendee::STATUS_*` constants)
 */
class EventRaidAttendee extends DatabaseObject implements ITitledLinkObject
{
    // states of column 'status'
    const STATUS_LOGIN = 0;

    const STATUS_CONFIRMED = 1;

    const STATUS_LOGOUT = 2;

    const STATUS_RESERVE = 3;

    /**
     * character profile object
     */
    protected ?CharacterProfile $character = null;

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'attendeeID';

    /**
     * event object
     */
    protected ?Event $event = null;

    /**
     * possible distribution
     * @var mixed[]
     */
    protected ?array $possibleDistribution = null;

    /**
     * Returns the character profile of the current attendee.
     */
    public function getCharacter(): ?CharacterProfile
    {
        if ($this->character === null && $this->characterID) {
            $this->character = CharacterProfileRuntimeCache::getInstance()->getObject($this->characterID);
        }

        return $this->character;
    }

    /**
     * Returns the event of the current attendee.
     */
    public function getEvent(): Event
    {
        if ($this->event === null) {
            $this->event = EventRuntimeCache::getInstance()->getObject($this->eventID);
        }

        return $this->event;
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return $this->getCharacter()->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->characterName;
    }

    /**
     * Returns the possible distribution of the current attendee.
     * 
     * @return  mixed[]
     */
    public function possibleDistribution(): array
    {
        if ($this->possibleDistribution === null) {
            $this->possibleDistribution = [];

            switch ($this->getEvent()->distributionMode) {
                case 'class':
                    $this->possibleDistribution[] = $this->classificationID;
                    break;
                case 'none':
                    $this->possibleDistribution[] = 'none';
                    break;
                case 'role':
                    $sql = "SELECT  roleID
                        FROM    rp" . WCF_N . "_classification_to_role
                        WHERE   classificationID = ?";
                    $statement = WCF::getDB()->prepareStatement($sql);
                    $statement->execute([$this->classificationID]);
                    $this->possibleDistribution = $statement->fetchAll(\PDO::FETCH_COLUMN);
                    break;
            }
        }

        return $this->possibleDistribution;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
