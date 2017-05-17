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


namespace Antares\Contracts\Html;

interface Grid
{

    /**
     * Add or append Grid attributes.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @return array|null
     */
    public function attributes($key = null, $value = null);

    /**
     * Set fieldset layout (view).
     *
     * <code>
     *      // use default horizontal layout
     *      $fieldset->layout('horizontal');
     *
     *      // use default vertical layout
     *      $fieldset->layout('vertical');
     *
     *      // define fieldset using custom view
     *      $fieldset->layout('path.to.view');
     * </code>
     *
     * @param  string  $name
     *
     * @return $this
     */
    public function layout($name, array $params = null);

    /**
     * Allow column overwriting.
     *
     * @param  string      $name
     * @param  mixed|null  $callback
     *
     * @return \Illuminate\Support\Fluent
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function of($name, $callback = null);

    /**
     * Forget meta value.
     *
     * @param  string  $key
     *
     * @return void
     */
    public function forget($key);

    /**
     * Get meta value.
     *
     * @param  string  $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Set meta value.
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return array
     */
    public function set($key, $value);
}
