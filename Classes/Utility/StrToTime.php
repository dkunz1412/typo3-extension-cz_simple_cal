<?php

namespace Tx\CzSimpleCal\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Christian Zenker <christian.zenker@599media.de>, 599media GmbH
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to enhance strtotime to use some more features that are available in PHP 5.3 in PHP 5.2
 *
 * I decided not to support the full extend of PHP 5.3 functionality, but only the
 * features that are needed most often.
 */
class StrToTime
{
    /**
     * @var array
     */
    public static $translateFirstDayOf = [
        'first day of this month' => '%Y-%m-01 %H:%M:%S|',
        'first day of last month' => '%Y-%m-01 %H:%M:%S -1 month|',
        'first day of next month' => '%Y-%m-01 %H:%M:%S +1 month|',
        'last day of this month' => '%Y-%m-01 %H:%M:%S +1 month -1 day|',
        'last day of last month' => '%Y-%m-01 %H:%M:%S -1 day|',
        'last day of next month' => '%Y-%m-01 %H:%M:%S +2 months -1 day|',
    ];

    /**
     * translates 3-lettered lowercased strings of a weekday to an integer
     *
     * @var array
     */
    public static $weekdayToInt = [
        'mon' => 1,
        'tue' => 2,
        'wed' => 3,
        'thu' => 4,
        'fri' => 5,
        'sat' => 6,
        'sun' => 7,
    ];

    /**
     * substitude the occurance of "first day of ..." and its brothers
     *
     * Actually PHP 5.3 understands those "first day of ..." phrases, but it seems a little
     * buggy when used with a DateTime Object. Therefore we will substitute this also.
     *
     * I encountered this bug when using PHP 5.3.2. Do the following:
     * <code>
     *    $foo = new DateTime("first day of this month");
     *    $foo->setDate(2009, 2, 13);
     *  echo $foo->format('Y-m-d');
     * </code>
     *
     * I would expect "2009-02-13" as return, but instead it shows "2009-02-01".
     * This bug would break your neck when using something like
     * "first day this month|monday this week" which is quite common when rendering
     * a month table.
     *
     * @param string $time
     * @return string
     */
    public static function doSubstitutionFirstDayOf($time)
    {
        return strtr($time, self::$translateFirstDayOf);
    }

    /**
     * fix the fact that weeks in php start with sundays, but ISO weeks start with mondays
     *
     * this would yield an inconsistency between PHP 5.2 and 5.3, as there would be no
     * way to generate times of in the week format where sunday is the first day of the week
     *
     * @param string $time
     * @return mixed
     */
    public static function doSubstitutionReltextWeek($time)
    {
        /** @noinspection PhpUndefinedConstantInspection */
        return preg_replace_callback(
            '/(mon(?:day)?|tue(?:sday)?|wed(?:nesday)?|thu(?:rsday)?|fri(?:day)?|sat(?:urday)?|sun(?:day)?)'
            . ' (last|this|next) week/i',
            [
                self,
                'callbackSubstitutedReltextWeekPattern',
            ],
            $time
        );
    }

    public static function doSubstitutions($time)
    {
        $time = self::doSubstitutionFirstDayOf($time);
        return self::doSubstitutionReltextWeek($time);
    }

    /**
     * a non-local version of strftime
     *
     * an alternative would be to switch the language local, but there
     * is no guarantee it is present and even if, the label might be hard
     * to guess. So this should be a rather light-weight solution.
     *
     * @param string $time
     * @param integer $now
     * @return string
     */
    public static function strftime($time, $now = null)
    {
        if (strpos($time, '%') !== false) {
            if (is_null($now)) {
                $now = time();
            }
            $time = strtr(
                $time,
                [
                    '%a' => date('l', $now),
                    '%A' => date('D', $now),
                    '%b' => date('M', $now),
                    '%B' => date('F', $now),
                    '%h' => date('M', $now),
                ]
            );
        }
        return strftime($time, $now);
    }

    /**
     * the enhanced strtotime()
     *
     * * adds PHP 5.3 functionality to PHP 5.2
     * * allows chaining of different phrases by using the pipe-symbol ("|")
     *
     * @param string $time
     * @param integer|false $now
     * @return bool|false|int|null
     */
    public static function strtotime($time, $now = null)
    {
        if (is_null($now)) {
            $now = time();
        }

        $time = self::doSubstitutions($time);

        foreach (GeneralUtility::trimExplode('|', $time, true) as $time) {
            $now = strtotime(self::strftime($time, $now), $now);
            if ($now === false) {
                return false;
            }
        }

        return $now;
    }

    /**
     * callback for regexp in doCommonSubstitutions()
     *
     * @see doCommonSubstitutions()
     * @param string $matches
     * @return string
     */
    protected static function callbackSubstitutedReltextWeekPattern($matches)
    {
        /**
         * the day of the week as 3-letter lowercased string (like "mon", "tue")
         *
         * @var string
         */
        $dow = strtolower(substr($matches[1], 0, 3));
        $whichWeek = $matches[2];

        $ret = '%G-W%V-' . self::$weekdayToInt[$dow];

        if ($whichWeek === 'last') {
            $ret .= ' -1 week';
        } elseif ($whichWeek === 'next') {
            $ret .= ' +1 week';
        }

        return $ret;
    }
}
