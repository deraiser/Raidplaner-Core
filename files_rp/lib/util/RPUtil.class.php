<?php

namespace rp\util;

use rp\data\event\Event;
use wcf\data\language\Language;
use wcf\data\user\User;
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
 * Contains raidplaner-related functions.
 * 
 * @author      Marco Daries
 * @package     Daries\RP\Util
 */
final class RPUtil
{
    /**
     * fake user object with `UTC` set as timezone
     */
    private static ?User $UTCUser = null;

    /**
     * Returns the full day format of a date.
     */
    public static function formatEventFullDay(\DateTime $time, ?Language $language = null): string
    {
        return DateUtil::format($time, Event::DATE_FORMAT, $language, self::getUTCUser());
    }

    /**
     * Formats the points
     */
    public static function formatPoints(float|int $points = 0): string
    {
        return StringUtil::formatDouble(
                $points,
                RP_ROUND_POINTS ? RP_ROUND_POINTS_PRECISION : 2
        );
    }

    /**
     * Returns a fake user object with `UTC` set as timezone.
     */
    private static function getUTCUser(): User
    {
        if (self::$UTCUser === null) {
            self::$UTCUser = new User(null, ['timezone' => 'UTC']);
        }

        return self::$UTCUser;
    }
}
