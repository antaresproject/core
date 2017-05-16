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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester;

use Illuminate\Http\Response;
use Antares\Tester\Dispatcher;

class Factory
{

    /**
     * Dispatcher instance.
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Response instance.
     *
     * @var Response
     */
    protected $response;

    /**
     * Construct a new Widgets instance.
     *
     * @param  Dispatcher  $dispatcher
     * @param  Response  $response
     */
    public function __construct(Dispatcher $dispatcher, Response $response)
    {
        $this->dispatcher = $dispatcher;
        $this->response   = $response;
    }

}
