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


namespace Antares\Contracts\Html\Form;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Antares\Contracts\Html\Grid as GridContract;

interface Grid extends GridContract
{

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $form->with(DB::table('users')->get());
     * </code>
     *
     * @param  array|\stdClass|\Illuminate\Database\Eloquent\Model  $row
     *
     * @return mixed
     */
    public function with($row = null);

    /**
     * Attach rows data instead of assigning a model.
     *
     * @param  array  $row
     *
     * @return mixed
     *
     * @see    static::with()
     */
    public function row($row = null);

    /**
     * Create a new Fieldset instance.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return \Antares\Contracts\Html\Form\Fieldset
     */
    public function fieldset($name, Closure $callback = null);

    /**
     * Find the existing Fieldset. It not exists then create a new instance.
     *
     * @param string $name
     * @param Closure|null $callback
     * @return \Antares\Contracts\Html\Form\Fieldset
     */
    public function findFieldsetOrCreateNew($name, Closure $callback = null);

    /**
     * Add hidden field.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     *
     * @return void
     */
    public function hidden($name, $callback = null);

    /**
     * Setup form configuration.
     *
     * @param  \Antares\Contracts\Html\Form\Presenter  $listener
     * @param  string  $url
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function resource(Presenter $listener, $url, Model $model, array $attributes = []);

    /**
     * Setup simple form configuration.
     *
     * @param  \Antares\Contracts\Html\Form\Presenter  $listener
     * @param  string  $url
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  $attributes
     *
     * @return $this
     */
    public function setup(Presenter $listener, $url, $model, array $attributes = []);
}
