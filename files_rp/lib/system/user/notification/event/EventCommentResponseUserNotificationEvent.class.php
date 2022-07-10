<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\cache\runtime\CommentRuntimeCache;
use wcf\system\email\Email;
use wcf\system\user\notification\event\AbstractSharedUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentResponseUserNotificationEvent;
use wcf\system\user\notification\object\CommentResponseUserNotificationObject;

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
 * User notification event for event comment responses.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\User\Notification\Object\Type
 *
 * @method      CommentResponseUserNotificationObject       getUserNotificationObject()
 */
class EventCommentResponseUserNotificationEvent extends AbstractSharedUserNotificationEvent implements ITestableUserNotificationEvent
{
    use TTestableCommentResponseUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;
    /**
     * @inheritDoc
     */
    protected $stackable = true;

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): array
    {
        $messageID = '<info.daries.rp.eventComment.notification/' . $this->getUserNotificationObject()->commentID . '@' . Email::getHost() . '>';

        return [
            'template' => 'email_notification_commentResponse',
            'in-reply-to' => [$messageID],
            'references' => [$messageID],
            'application' => 'wcf',
            'variables' => [
                'commentID' => $this->getUserNotificationObject()->commentID,
                'eventObj' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->additionalData['objectID']),
                'languageVariablePrefix' => 'rp.user.notification.eventComment.response',
                'responseID' => $this->getUserNotificationObject()->responseID,
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getEventHash(): string
    {
        return \sha1($this->eventID . '-' . $this->notification->objectID);
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->additionalData['objectID'])->getLink() . '#comment' . $this->getUserNotificationObject()->commentID . '/response' . $this->getUserNotificationObject()->responseID;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.response.message.stacked',
                    [
                        'author' => $this->author,
                        'authors' => \array_values($authors),
                        'commentID' => $this->getUserNotificationObject()->commentID,
                        'count' => $count,
                        'event' => ViewableEventRuntimeCache::getInstance()
                            ->getObject($this->additionalData['objectID']),
                        'guestTimesTriggered' => $this->notification->guestTimesTriggered,
                        'others' => $count - 1,
                        'responseID' => $this->getUserNotificationObject()->responseID,
                    ]
            );
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.response.message', [
                'author' => $this->author,
                'commentID' => $this->getUserNotificationObject()->commentID,
                'event' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->additionalData['objectID']),
                'responseID' => $this->getUserNotificationObject()->responseID,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        $count = \count($this->getAuthors());
        if ($count > 1) {
            return $this->getLanguage()->getDynamicVariable(
                    'rp.user.notification.eventComment.response.title.stacked',
                    [
                        'count' => $count,
                        'timesTriggered' => $this->notification->timesTriggered,
                    ]
            );
        }

        return $this->getLanguage()->get('rp.user.notification.eventComment.response.title');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        CommentRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->commentID);
    }
}
