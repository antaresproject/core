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
 * @package    UI
 * @version    0.9.2
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI;

use Closure;
use Countable;
use IteratorAggregate;
use Antares\Support\Nesty;

abstract class Handler implements Countable, IteratorAggregate
{

    /**
     * Nesty instance.
     *
     * @var \Antares\Support\Nesty
     */
    protected $nesty;

    /**
     * Name of this instance.
     *
     * @var string
     */
    protected $name;

    /**
     * Name of this instance.
     *
     * @var string
     */
    protected $content;

    /**
     * Widget configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Type of widget.
     *
     * @var string
     */
    protected $type;

    /**
     * Construct a new instance.
     *
     * @param  string  $name
     * @param  array   $config
     */
    public function __construct($name, array $config = [])
    {
        $this->config = array_merge($config, $this->config);

        $this->name  = $name;
        $this->nesty = new Nesty($this->config);
    }

    /**
     * Add an item to current widget.
     *
     * @param  string  $id
     * @param  string|\Closure  $location
     * @param  \Closure|null  $callback
     *
     * @return mixed
     */
    abstract public function add($id, $location = 'parent', $callback = null);

    /**
     * Attach item to current widget.
     *
     * @param  string           $id
     * @param  string|\Closure  $location
     * @param  \Closure|null    $callback
     *
     * @return mixed
     */
    protected function addItem($id, $location = 'parent', $callback = null)
    {


        if ($location instanceof Closure) {
            $callback = $location;
            $location = 'parent';
        }


        $item = $this->nesty->add($id, $location ?: 'parent');
        if ($callback instanceof Closure) {
            call_user_func($callback, $item);
        }

        return $item;
    }

    /**
     * Get an instance of item from current widget.
     *
     * @param  string  $id
     *
     * @return mixed
     */
    public function has($id)
    {

        return $this->nesty->has($id);
    }

    /**
     * Get if the instance has an item.
     *
     * @param  string  $id
     *
     * @return mixed
     */
    public function is($id)
    {
        return $this->nesty->is($id);
    }

    /**
     * Get all item from Nesty.
     *
     * @return array
     *
     * @see \Antares\Support\Nesty::items()
     */
    public function items()
    {
        return $this->nesty->items();
    }

    /**
     * Get the number of items for the current page.
     *
     * @return int
     */
    public function count()
    {
        return $this->nesty->items()->count();
    }

    /**
     * Get an iterator for the items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getIterator()
    {
        return $this->nesty->items();
    }

    /**
     * Nesty getter
     * 
     * @return Nesty
     */
    public function nesty()
    {
        return $this->nesty;
    }

    public function setNesty(Nesty $nesty)
    {
        $this->nesty = $nesty;
        return $this;
    }

}
