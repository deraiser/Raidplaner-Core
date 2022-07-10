<?php

namespace rp\system\event\discussion;

use rp\data\event\Event;

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
 * Discussion provider for events.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event\Discussion
 */
interface IEventDiscussionProvider
{

    /**
     * Returns the number of discussion items.
     */
    public function getDiscussionCount(): int;

    /**
     * Returns the simple phrase "X <discussions>" that is used for both the statistics
     * and the meta data in the event's headline.
     */
    public function getDiscussionCountPhrase(): string;

    /**
     * Returns the permalink to the discussions or an empty string if there is none.
     */
    public function getDiscussionLink(): string;

    /**
     * Returning true will assign this provider to the event, otherwise the next
     * possible provider is being evaluated.
     */
    public static function isResponsible(Event $event): bool;

    /**
     * Renders the input and display section of the associated discussion.
     */
    public function renderDiscussions(): string;
}
