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



namespace Antares\Logger\Model;

use Antares\Logger\Traits\LogRecorder;
use Antares\Model\Eloquent;

class Recorder extends Eloquent
{

    use LogRecorder;

    /**
     * @var bool
     */
    protected $auditEnabled = true;

    /**
     * @var array
     */
    protected $auditableTypes = ['created', 'saved', 'deleted'];

    /**
     * @var string
     */
    public static $logCustomMessage = '{type} in {created_at}';

    /**
     * @var array
     */
    public static $logCustomFields = [];
    // Disables the log record after 500 records.
    protected $historyLimit        = 500;
    // Fields you do NOT want to register.
    protected $dontKeepLogOf       = ['created_at', 'updated_at'];

}
