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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension\Processor;

use Closure;
use Illuminate\Support\Fluent;
use Antares\Contracts\Extension\Factory;
use Antares\Contracts\Publisher\FilePermissionException;
use Illuminate\Support\Facades\Log;
use Exception;

abstract class Processor
{

    /**
     * The extension factory implementation.
     *
     * @var \Antares\Contracts\Extension\Factory
     */
    protected $factory;

    /**
     * Construct a new processor instance.
     *
     * @param \Antares\Contracts\Extension\Factory  $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Execute extension processing.
     *
     * @param  object  $listener
     * @param  string  $type
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  \Closure  $callback
     *
     * @return mixed
     */
    protected function execute($listener, $type, Fluent $extension, Closure $callback)
    {
        $name = $extension->get('name');

        try {
            call_user_func($callback, $this->factory, $name);
        } catch (FilePermissionException $e) {
            Log::emergency($e);
            return call_user_func([$listener, "{$type}HasFailed"], $extension, ['error' => $e->getMessage()]);
        } catch (Exception $e) {
            Log::emergency($e);
            return call_user_func([$listener, "{$type}HasFailed"], $extension, ['error' => $e->getMessage()]);
        }

        return call_user_func([$listener, "{$type}HasSucceed"], $extension);
    }

}
