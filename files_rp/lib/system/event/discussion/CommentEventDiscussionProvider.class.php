<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;
use wcf\system\comment\CommentHandler;
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
 * The built-in discussion provider using the native comment system.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event\Discussion
 */
class CommentEventDiscussionProvider extends AbstractEventDiscussionProvider
{

    /**
     * @inheritDoc
     */
    public function getDiscussionCount(): int
    {
        return $this->event->comments;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase(): string
    {
        return WCF::getLanguage()->getDynamicVariable('rp.event.eventComments', [
                'event' => $this->event,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink(): string
    {
        return $this->event->getLink() . '#comments';
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Event $event): bool
    {
        return !!$event->enableComments;
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions(): string
    {
        $commentCanAdd = WCF::getSession()->getPermission('user.rp.canAddComment');
        $commentObjectTypeID = CommentHandler::getInstance()->getObjectTypeID('info.daries.rp.eventComment');
        $commentManager = CommentHandler::getInstance()->getObjectType($commentObjectTypeID)->getProcessor();
        $commentList = CommentHandler::getInstance()->getCommentList(
            $commentManager,
            $commentObjectTypeID,
            $this->event->eventID
        );

        WCF::getTPL()->assign([
            'commentCanAdd' => $commentCanAdd,
            'commentList' => $commentList,
            'commentObjectTypeID' => $commentObjectTypeID,
            'lastCommentTime' => $commentList->getMinCommentTime(),
            'likeData' => (MODULE_LIKE) ? $commentList->getLikeData() : [],
        ]);

        return WCF::getTPL()->fetch('eventComments', 'rp');
    }
}
