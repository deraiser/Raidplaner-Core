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
 * Represents a non-existing discussion provider and is used when there is no other
 * type of discussion being available. This provider is always being evaluated last.
 *
 * @author      Marco Daries
 * @package     Daries\RP\System\Event\Discussion
 */
class VoidEventDiscussionProvider extends AbstractEventDiscussionProvider
{

    /**
     * @inheritDoc
     */
    public function getDiscussionCount(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionCountPhrase(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getDiscussionLink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public static function isResponsible(Event $event): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function renderDiscussions(): string
    {
        return '';
    }
}
