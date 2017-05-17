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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Exceptions;

use Throwable;
use Exception;

/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 12:54
 */
class WrongSelectOptionFormatException extends Exception
{

    /**
     * WrongSelectOptionFormatException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @throws Exception
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        throw new Exception($message, $code, $previous);
    }
}