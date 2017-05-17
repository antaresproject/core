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

class DateFormatter
{

    /**
     * creates representation of human readable date in the past
     * 
     * @param string $date
     * @param String $html
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

}
