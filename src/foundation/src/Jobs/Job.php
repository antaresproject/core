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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Jobs;

use Illuminate\Bus\Queueable;

abstract class Job
{
    /*
      |--------------------------------------------------------------------------
      | Queueable Jobs
      |--------------------------------------------------------------------------
      |
      | This job base class provides a central location to place any logic that
      | is shared across all of your jobs. The trait included with the class
      | provides access to the "queueOn" and "delay" queue helper methods.
      |
     */

use Queueable;
}
