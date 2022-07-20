<?php

namespace rp\data\event;

use rp\data\event\legend\EventLegend;
use rp\data\event\legend\EventLegendCache;
use rp\system\calendar\Day;
use rp\system\calendar\Month;
use rp\system\event\discussion\CommentEventDiscussionProvider;
use rp\system\event\discussion\IEventDiscussionProvider;
use rp\system\event\discussion\VoidEventDiscussionProvider;
use rp\system\event\IEventController;
use rp\util\RPUtil;
use wcf\data\DatabaseObject;
use wcf\data\IUserContent;
use wcf\data\language\Language;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\TUserContent;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\html\output\HtmlOutputProcessor;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\StringUtil;

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
 * Represents a event.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Data\Event
 * 
 * @property-read   int         $eventID                unique id of the event
 * @property-read   int|null    $objectTypeID           id of the event controller object type
 * @property-read   string      $title                  name of the event
 * @property-read   int|null    $userID                 id of the user who created the event or `null` if the user does not exist anymore
 * @property-read   string      $username               name of the user who created the event
 * @property-read   int         $created                timestamp at which the event has been created
 * @property-read   int         $startTime              timestamp for start the event
 * @property-read   int         $endTime                timestamp for end the event
 * @property-read   int         $isFullDay              is `1` if the event occurs all day long, otherwise `0`
 * @property-read   string      $notes                  notes of the event
 * @property-read   int         $views                  number of times the event has been viewed
 * @property-read   int         $enableComments         is `1` if comments are enabled for the event, otherwise `0`
 * @property-read   int         $comments               number of comments on the event
 * @property-read   int         $cumulativeLikes        cumulative result of likes (counting `+1`) and dislikes (counting `-1`) for the event
 * @property-read   int         $hasEmbeddedObjects     is `1` if there are embedded objects in the event, otherwise `0`
 * @property-read	int         $deleteTime             timestamp at which the event has been deleted
 * @property-read	int|null    $raidID                 raid id after a raid event has been converted to a raid or `null` if not converted to a raid
 * @property-read	int|null    $legendID               legend id individual color selection or `null` if not specified
 * @property-read	int         $isDeleted              is `1` if the event is in trash bin, otherwise `0`
 * @property-read   int         $isCanceled             is `1` if the even is canceled, otherwise `0`
 * @property-read   int         $isDisabled             is `1` if the even is disabled, otherwise `0`
 * @property-read   array       $additionalData         array with additional data of the event
 */
class Event extends DatabaseObject implements IUserContent, IRouteController
{
    use TUserContent;
    /**
     * name of the default date format language variable
     * @var string
     */
    const DATE_FORMAT = 'rp.event.dateFormat';

    /**
     * event controller
     */
    protected ?IEventController $controller = null;

    /**
     * discussion provider
     */
    protected ?IEventDiscussionProvider $discussionProvider = null;

    /**
     * event days
     */
    protected ?array $eventDays = null;

    /**
     * date time zone object
     */
    protected ?\DateTimeZone $timezoneObj = null;

    /**
     * Returns true if the current user can cancel this event.
     */
    public function canCancel(): bool
    {
        if (!$this->isRaidEvent()) {
            return false;
        }

        if ($this->canEdit()) {
            return true;
        }

        if ($this->getController()->isLeader()) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can delete these event.
     */
    public function canDelete(): bool
    {
        // check mod permissions
        if (WCF::getSession()->getPermission('mod.rp.canDeleteEventCompletely')) {
            return true;
        }

        if ($this->isRaidEvent()) {
            if ($this->getController()->isLeader()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the current user can edit these event.
     */
    public function canEdit(): bool
    {
        // check mod permissions
        if (WCF::getSession()->getPermission('mod.rp.canEditEvent')) {
            return true;
        }

        if ($this->isRaidEvent()) {
            if ($this->getController()->isLeader()) {
                return true;
            }
        }

        if ($this->userID && $this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canEditEvent')) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the given user has access to this event. If the given $user is null,
     * the function uses the current user.
     */
    public function canRead(?UserProfile $user = null): bool
    {
        if ($user === null) {
            $user = new UserProfile(WCF::getUser());
        }

        if ($this->isDeleted) {
            if (!$user->getPermission('mod.rp.canViewDeletedEvent')) {
                return false;
            }
        }

        if ($this->isDisabled) {
            if (!$user->getPermission('mod.rp.canModerateEvent')) {
                return false;
            }
        }

        if (!$user->getPermission('user.rp.canReadEvent')) {
            return false;
        }

        return true;
    }

    /**
     * Returns true if the current user can restore this event.
     */
    public function canRestore(): bool
    {
        if (WCF::getSession()->getPermission('mod.rp.canRestoreEvent')) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if the current user can trash this event.
     */
    public function canTrash(): bool
    {
        if (WCF::getSession()->getPermission('mod.rp.canDeleteEvent')) {
            return true;
        }

        // check user permissions
        if ($this->userID && $this->userID == WCF::getUser()->userID && WCF::getSession()->getPermission('user.rp.canDeleteEvent')) {
            return true;
        }

        return false;
    }

    /**
     * Returns the list of the available discussion providers.
     *
     * @return      string[]
     */
    public static function getAllDiscussionProviders(): array
    {
        /** @var string[] $discussionProviders */
        static $discussionProviders;

        if ($discussionProviders === null) {
            $discussionProviders = [];

            $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('info.daries.rp.event.discussionProvider');
            $commentProvider = '';
            foreach ($objectTypes as $objectType) {
                // the comment and the "void" provider should always be the last in the list
                if ($objectType->className === CommentEventDiscussionProvider::class) {
                    $commentProvider = $objectType->className;
                    continue;
                }

                $discussionProviders[] = $objectType->className;
            }

            $discussionProviders[] = $commentProvider;
            $discussionProviders[] = VoidEventDiscussionProvider::class;
        }

        return $discussionProviders;
    }

    /**
     * Returns the event controller.
     */
    public function getController(): IEventController
    {
        if ($this->controller === null) {
            $className = ObjectTypeCache::getInstance()->getObjectType($this->objectTypeID)->className;

            $this->controller = new $className();
            $this->controller->setEvent($this);
        }

        return $this->controller;
    }

    public function getCustomCSS(): string
    {
        $custom = '';

        switch ($this->legendType) {
            case EventLegend::TYPE_INDIVIDUAL:
                if ($this->customBGColor || $this->customFrontColor) {
                    $color = StringUtil::trim($this->customBGColor);
                    $custom = " style='background-color: {$color};'";
                }
                break;
            case EventLegend::TYPE_SELECT:
                if ($this->legendID) {
                    $legend = EventLegendCache::getInstance()->getLegendByID($this->legendID);

                    $color = StringUtil::trim($legend->bgColor);
                    $custom = " style='background-color: {$color};'";
                }
                break;
        }

        return $custom;
    }

    /**
     * Returns the responsible discussion provider for this event.
     */
    public function getDiscussionProvider(): IEventDiscussionProvider
    {
        if ($this->discussionProvider === null) {
            foreach (self::getAllDiscussionProviders() as $discussionProvider) {
                if (\call_user_func([$discussionProvider, 'isResponsible'], $this)) {
                    $this->setDiscussionProvider(new $discussionProvider($this));
                    break;
                }
            }

            if ($this->discussionProvider === null) {
                throw new \RuntimeException('No discussion provider has claimed to be responsible for the event #' . $this->eventID);
            }
        }

        return $this->discussionProvider;
    }

    /**
     * Returns the days on which the event runs.
     */
    public function getEventDays(): array
    {
        if ($this->eventDays === null) {
            $this->eventDays = [];

            $startTime = $this->startTime;
            while ($startTime < $this->endTime) {
                $s = DateUtil::getDateTimeByTimestamp($startTime);

                if (!$this->isFullDay) {
                    $s->setTimezone(WCF::getUser()->getTimeZone());
                }

                $day = $s->format('Y-m-d');

                if (!\in_array($day, $this->eventDays)) {
                    $this->eventDays[$day] = new Day($s->format('j'), new Month($s->format('n'), $s->format('Y')));
                }

                $startTime += 86400;
            }

            $e = DateUtil::getDateTimeByTimestamp($this->endTime);
            if (!$this->isFullDay) $e->setTimezone(WCF::getUser()->getTimeZone());
            $day = $e->format('Y-m-d');
            if (!\in_array($day, $this->eventDays)) $this->eventDays[$day] = new Day($e->format('j'), new Month($e->format('n'), $e->format('Y')));
        }

        return $this->eventDays;
    }

    /**
     * Returns a simplified message (only inline codes), truncated to 255 characters by default.
     */
    public function getExcerpt(int $maxLength = 255): string
    {
        return StringUtil::truncateHTML($this->getSimplifiedFormattedNotes(), $maxLength);
    }

    /**
     * Returns the event's formatted notes.
     */
    public function getFormattedNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->enableUgc = false;
        $processor->process($this->notes, 'info.daries.rp.event.notes', $this->eventID, false);

        return $processor->getHtml();
    }

    /**
     * Returns the event's formatted plain notes.
     */
    public function getFormattedPlainNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/plain');
        $processor->enableUgc = false;
        $processor->process($this->notes, 'info.daries.rp.event.notes', $this->eventID, false);

        return StringUtil::encodeHTML(StringUtil::truncate($processor->getHtml(), 255));
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size = null): string
    {
        return $this->getController()->getIcon($size);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Event', [
                'application' => 'rp',
                'object' => $this,
                'forceFrontend' => true
        ]);
    }

    public function getFormattedEndTime(?Language $language = null, bool $local = false): string
    {
        return $this->getFormattedTime($this->endTime, $language, $local);
    }

    public function getFormattedStartTime(?Language $language = null, bool $local = false): string
    {
        return $this->getFormattedTime($this->startTime, $language, $local);
    }

    /**
     * Returns a formatted version of the time.
     */
    private function getFormattedTime(int $time, ?Language $language = null, bool $local = false): string
    {
        if ($language === null) {
            $language = WCF::getLanguage();
        }

        $dateTime = DateUtil::getDateTimeByTimestamp($time);

        if ($this->isFullDay) {
            return RPUtil::formatEventFullDay($dateTime, $language);
        } else {
            $user = null;
            if ($local) {
                // fake user to show local timezone
                $user = new User(null, ['timezone' => $this->getTimeZone()->getName()]);
            }
            return \str_replace(
                '%time%',
                DateUtil::format(
                    $dateTime,
                    DateUtil::TIME_FORMAT,
                    $language,
                    $user
                ),
                \str_replace(
                    '%date%',
                    DateUtil::format(
                        $dateTime,
                        self::DATE_FORMAT,
                        $language,
                        $user
                    ),
                    $language->get('wcf.date.dateTimeFormat')
                )
            );
        }
    }

    /**
     * Returns the formatted time frame of this event.
     */
    public function getFormattedTimeFrame(?Language $language = null, bool $local = false): string
    {
        if (!$this->startTime || !$this->endTime) return '';

        if ($language === null) {
            $language = WCF::getLanguage();
        }

        $startDateTime = DateUtil::getDateTimeByTimestamp($this->startTime);
        $endDateTime = DateUtil::getDateTimeByTimestamp($this->endTime);

        $user = null;
        if (!$this->isFullDay) {
            $startDateTime->setTimezone($local ? $this->getTimeZone() : WCF::getUser()->getTimeZone());
            $endDateTime->setTimezone($local ? $this->getTimeZone() : WCF::getUser()->getTimeZone());

            if ($local) {
                // fake user to show local timezone
                $user = new User(null, ['timezone' => $this->getTimeZone()->getName()]);
            }
        }

        if ($startDateTime->format('Ymd') != $endDateTime->format('Ymd')) {
            // multiple days
            if ($this->isFullDay) {
                return RPUtil::formatEventFullDay($startDateTime, $language) . ' - ' . RPUtil::formatEventFullDay($endDateTime, $language);
            } else {
                return \str_replace(
                        '%time%',
                        DateUtil::format(
                            $startDateTime,
                            DateUtil::TIME_FORMAT,
                            $language,
                            $user
                        ),
                        \str_replace(
                            '%date%',
                            DateUtil::format(
                                $startDateTime,
                                self::DATE_FORMAT,
                                $language,
                                $user
                            ),
                            $language->get('wcf.date.dateTimeFormat')
                        )
                    ) . ' - ' . \str_replace(
                        '%time%',
                        DateUtil::format(
                            $endDateTime,
                            DateUtil::TIME_FORMAT,
                            $language,
                            $user
                        ),
                        \str_replace(
                            '%date%',
                            DateUtil::format(
                                $endDateTime,
                                self::DATE_FORMAT,
                                $language,
                                $user
                            ),
                            $language->get('wcf.date.dateTimeFormat')
                        )
                );
            }
        } else {
            // single day
            if ($this->isFullDay) {
                return RPUtil::formatEventFullDay($startDateTime, $language);
            } else {
                return \str_replace(
                        '%time%',
                        DateUtil::format(
                            $startDateTime,
                            DateUtil::TIME_FORMAT,
                            $language,
                            $user
                        ),
                        \str_replace(
                            '%date%',
                            DateUtil::format(
                                $startDateTime,
                                self::DATE_FORMAT,
                                $language,
                                $user
                            ),
                            $language->get('wcf.date.dateTimeFormat')
                        )
                    ) . ' - ' . DateUtil::format(
                        $endDateTime,
                        DateUtil::TIME_FORMAT,
                        $language,
                        $user
                );
            }
        }
    }

    /**
     * Returns `true` if this event is a raid event, otherwise `false`.
     */
    public function isRaidEvent(): bool
    {
        if ($this->getController()->getObjectTypeName() === 'info.daries.rp.event.raid') return true;
        return false;
    }

    /**
     * Sets the discussion provider for this event.
     */
    public function setDiscussionProvider(IEventDiscussionProvider $discussionProvider): void
    {
        $this->discussionProvider = $discussionProvider;
    }

    /**
     * Returns a simplified version of the formatted notes.
     */
    public function getSimplifiedFormattedNotes(): string
    {
        $processor = new HtmlOutputProcessor();
        $processor->setOutputType('text/simplified-html');
        $processor->process($this->notes, 'info.daries.rp.event.notes', $this->eventID);

        return $processor->getHtml();
    }

    /**
     * @inheritDoc
     */
    public function getTime()
    {
        return $this->created;
    }

    /**
     * Returns the time zone object for this event.
     *
     * @return	\DateTimeZone
     */
    public function getTimeZone(): \DateTimeZone
    {
        if ($this->timezoneObj === null) {
            if (!empty($this->timezone)) {
                try {
                    $this->timezoneObj = new \DateTimeZone($this->timezone);
                } catch (\Exception $e) {
                    
                }
            }
            if ($this->timezoneObj === null) {
                $this->timezoneObj = new \DateTimeZone('UTC');
            }
        }

        return $this->timezoneObj;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        if (empty($this->title)) {
            return $this->getController()->getTitle();
        }

        return $this->title;
    }

    /**
     * @inheritDoc
     */
    protected function handleData($data): void
    {
        parent::handleData($data);

        // handle condition data
        if (isset($data['additionalData'])) {
            $this->data['additionalData'] = @\unserialize($data['additionalData'] ?: '');

            if (!\is_array($this->data['additionalData'])) {
                $this->data['additionalData'] = [];
            }
        } else {
            $this->data['additionalData'] = [];
        }
    }

    /**
     * @inheritDoc
     */
    public function __get($name): mixed
    {
        $value = parent::__get($name);

        if ($value === null && isset($this->data['additionalData'][$name])) {
            $value = $this->data['additionalData'][$name];
        }

        return $value;
    }
}
