<?php

namespace rp\system\comment\manager;

use rp\data\event\EventEditor;
use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\cache\runtime\ViewableCommentResponseRuntimeCache;
use wcf\system\cache\runtime\ViewableCommentRuntimeCache;
use wcf\system\comment\manager\AbstractCommentManager;
use wcf\system\like\IViewableLikeProvider;
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
 * Event comment manager implementation.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Comment\Manager
 */
class EventCommentManager extends AbstractCommentManager implements IViewableLikeProvider
{
    /**
     * @inheritDoc
     */
    protected $permissionAdd = 'user.rp.canAddComment';

    /**
     * @inheritDoc
     */
    protected $permissionAddWithoutModeration = 'user.rp.canAddCommentWithoutModeration';

    /**
     * @inheritDoc
     */
    protected $permissionCanModerate = 'mod.rp.canModerateComment';

    /**
     * @inheritDoc
     */
    protected $permissionDelete = 'user.rp.canDeleteComment';

    /**
     * @inheritDoc
     */
    protected $permissionEdit = 'user.rp.canEditComment';

    /**
     * @inheritDoc
     */
    protected $permissionModDelete = 'mod.rp.canDeleteComment';

    /**
     * @inheritDoc
     */
    protected $permissionModEdit = 'mod.rp.canEditComment';

    /**
     * @inheritDoc
     */
    public function getLink($objectTypeID, $objectID): string
    {
        return (EventRuntimeCache::getInstance()->getObject($objectID))->getLink();
    }

    /**
     * @inheritDoc
     */
    public function getTitle($objectTypeID, $objectID, $isResponse = false): string
    {
        if ($isResponse) return WCF::getLanguage()->get('rp.event.commentResponse');
        return WCF::getLanguage()->getDynamicVariable('rp.event.comment');
    }

    /**
     * @inheritDoc
     */
    public function isAccessible($objectID, $validateWritePermission = false): bool
    {
        // check object id
        $event = EventRuntimeCache::getInstance()->getObject($objectID);
        if ($event === null || !$event->canRead()) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function isContentAuthor($commentOrResponse): bool
    {
        $event = ViewableEventRuntimeCache::getInstance()->getObject($this->getObjectID($commentOrResponse));

        if ($event->isRaidEvent()) {
            if ($event->leaders) {
                if (\in_array($commentOrResponse->userID, $event->leaders)) {
                    return true;
                }
            }
            return false;
        }

        return $commentOrResponse->userID && $event->userID == $commentOrResponse->userID;
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $likes): void
    {
        $commentLikeObjectType = ObjectTypeCache::getInstance()
            ->getObjectTypeByName('com.woltlab.wcf.like.likeableObject', 'com.woltlab.wcf.comment');

        $commentIDs = $responseIDs = [];
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                $commentIDs[] = $like->objectID;
            } else {
                $responseIDs[] = $like->objectID;
            }
        }

        // fetch response
        $userIDs = $responses = [];
        if (!empty($responseIDs)) {
            $responses = ViewableCommentResponseRuntimeCache::getInstance()->getObjects($responseIDs);

            foreach ($responses as $response) {
                $commentIDs[] = $response->commentID;
                if ($response->userID) {
                    $userIDs[] = $response->userID;
                }
            }
        }

        // fetch comments
        $comments = ViewableCommentRuntimeCache::getInstance()->getObjects($commentIDs);

        // fetch users
        $users = [];
        $eventIDs = [];
        foreach ($comments as $comment) {
            $eventIDs[] = $comment->objectID;
            if ($comment->userID) {
                $userIDs[] = $comment->userID;
            }
        }
        if (!empty($userIDs)) {
            $users = UserProfileRuntimeCache::getInstance()->getObjects(\array_unique($userIDs));
        }

        // fetch articles
        $events = [];
        if (!empty($eventIDs)) {
            $events = EventRuntimeCache::getInstance()->getObjects($eventIDs);
        }

        // set message
        foreach ($likes as $like) {
            if ($like->objectTypeID == $commentLikeObjectType->objectTypeID) {
                // comment like
                if (isset($comments[$like->objectID])) {
                    $comment = $comments[$like->objectID];

                    if (
                        isset($events[$comment->objectID]) &&
                        $events[$comment->objectID] !== null &&
                        $events[$comment->objectID]->canRead()
                    ) {
                        $like->setIsAccessible();

                        // short output
                        $text = WCF::getLanguage()->getDynamicVariable(
                            'wcf.like.title.info.daries.rp.eventComment',
                            [
                                'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                                'comment' => $comment,
                                'event' => $events[$comment->objectID],
                                'reaction' => $like,
                            ]
                        );
                        $like->setTitle($text);

                        // output
                        $like->setDescription($comment->getExcerpt());
                    }
                }
            } else {
                // response like
                if (isset($responses[$like->objectID])) {
                    $response = $responses[$like->objectID];
                    $comment = $comments[$response->commentID];

                    if (
                        isset($events[$comment->objectID]) &&
                        $events[$comment->objectID] !== null &&
                        $events[$comment->objectID]->canRead()
                    ) {
                        $like->setIsAccessible();

                        // short output
                        $text = WCF::getLanguage()->getDynamicVariable(
                            'wcf.like.title.info.daries.rp.eventComment.response',
                            [
                                'responseAuthor' => $comment->userID ? $users[$response->userID] : null,
                                'commentAuthor' => $comment->userID ? $users[$comment->userID] : null,
                                'event' => $events[$comment->objectID],
                                'reaction' => $like,
                                'response' => $response,
                            ]
                        );
                        $like->setTitle($text);

                        // output
                        $like->setDescription($response->getExcerpt());
                    }
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function updateCounter($objectID, $value): void
    {
        $event = EventRuntimeCache::getInstance()->getObject($objectID);
        $editor = new EventEditor($event);
        $editor->updateCounters([
            'comments' => $value,
        ]);
    }
}
