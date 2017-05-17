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


namespace Antares\Html\Form;

use Closure;
use Antares\Html\Factory as BaseFactory;
use Antares\Contracts\Html\Form\Factory as FactoryContract;

class Factory extends BaseFactory implements FactoryContract
{

    /**
     * {@inheritdoc}
     */
    public function make(Closure $callback = null)
    {
        $builder = new FormBuilder(new Grid($this->app), new ClientScript($this->app), $this->app);
        return $builder->extend($callback);
    }

    /**
     * Allow to access `form` service location method using magic method.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->app->make('form'), $method], $parameters);
    }

}
