<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Utilities;

class LogParser
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Parsed data
     *
     * @var array
     */
    protected static $parsed = [];

    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Parse file content.
     *
     * @param  string  $raw
     *
     * @return array
     */
    public static function parse($raw)
    {
        self::$parsed = [];
        list($headings, $data) = self::parseRawData($raw);

        if (!is_array($headings)) {
            // @codeCoverageIgnoreStart
            return self::$parsed;
            // @codeCoverageIgnoreEnd
        }

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                self::populateEntries($heading, $data, $i);
            }
        };

        unset($headings, $data);

        return array_reverse(self::$parsed);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Parse raw data.
     *
     * @param  string  $raw
     *
     * @return array
     */
    private static function parseRawData($raw)
    {
        $pattern = '/\[' . REGEX_DATE_PATTERN . ' ' . REGEX_TIME_PATTERN . '\].*/';
        preg_match_all($pattern, $raw, $headings);
        $data    = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param  array  $heading
     * @param  array  $data
     * @param  int    $key
     */
    private static function populateEntries($heading, $data, $key)
    {
        foreach (LogLevels::all() as $level) {
            if (self::hasLogLevel($heading[$key], $level)) {
                self::$parsed[] = [
                    'level'  => $level,
                    'header' => $heading[$key],
                    'stack'  => $data[$key]
                ];
            }
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param  string  $heading
     * @param  string  $level
     *
     * @return bool
     */
    private static function hasLogLevel($heading, $level)
    {
        return str_contains(strtolower($heading), strtolower('.' . $level));
    }

}
