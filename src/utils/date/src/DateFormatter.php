<?php

/**
 * Part of the Antares package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Date;

use Carbon\Carbon;
use Antares\Brands\Model\DateFormat;

class DateFormatter
{

    /**
     * @var DateFormat|null|false
     */
    protected static $formatDateModel = false;

    /**
     * creates representation of human readable date in the past
     * 
     * @param string $date
     * @param bool $html
     * @return String
     */
    public function formatTimeAgoForHumans($date, $html = true)
    {
        $value = Carbon::createFromTimeStamp(strtotime($date))->diffForHumans();
        return ($html) ? $this->decorate($value, $date) : $value;
    }

    /**
     * creates html representation of human date
     * 
     * @param String $value
     * @param String $original
     * @return String
     */
    protected function decorate($value, $original)
    {
        return '<span data-tooltip-inline="' . $original . '" >' . $value . '</span>';
    }

    /**
     * @param $date
     * @param string|null $format
     * @return false|string
     */
    public static function formatDate($date, string $format = null)
    {

        if ($format === null) {
            if (self::$formatDateModel === false) {
                $dateFormatId          = app('antares.memory')->make('registry')->get('brand.configuration.options.date_format_id');
                self::$formatDateModel = DateFormat::query()->find($dateFormatId);
            }


            $format = self::$formatDateModel ? self::$formatDateModel->format : 'y-m-d';
        }
        if ($date instanceof Carbon) {
            return $date->format($format);
        }

        $time = is_numeric($date) ? $date : strtotime($date);

        return date($format, $time);
    }

    /**
     * @param $date
     * @param string|null $format
     * @return false|string
     */
    public static function formatTime($date, string $format = null)
    {
        if (is_null($format)) {
            $timeFormat = app('antares.memory')->make('registry')->get('brand.configuration.options.time_format');
            $format     = $timeFormat ?? 'H:i:s';
        }
        $time = is_numeric($date) ? $date : strtotime($date);
        return date($format, $time);
    }

}
