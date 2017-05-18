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


namespace Antares\Support;

use Illuminate\Support\Arr;
use Antares\Support\Traits\DescendibleTrait;

class Nesty
{

    use DescendibleTrait;

    /**
     * List of items.
     *
     * @var array
     */
    public $items = [];

    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Construct a new instance.
     *
     * @param  array  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Create a new Fluent instance while appending default config.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function toFluent($id)
    {
        $defaults = Arr::get($this->config, 'defaults', []);
        return new Fluent(array_merge($defaults, [
                    'id'     => $id,
                    'childs' => []
        ]));
    }

    /**
     * Add item before reference $before.
     *
     * @param  string  $id
     * @param  string  $before
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function addBefore($id, $before)
    {
        $items    = [];
        $item     = $this->toFluent($id);
        $keys     = array_keys($this->items);
        $position = array_search($before, $keys);

        if ($position === false) {
            return $this->addParent($id);
        }

        foreach ($keys as $key => $fluent) {
            if ($key === $position) {
                $items[$id] = $item;
            }

            $items[$fluent] = $this->items[$fluent];
        }

        $this->items = $items;

        return $item;
    }

    /**
     * Add item after reference $after.
     *
     * @param  string  $id
     * @param  string  $after
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function addAfter($id, $after)
    {
        $items = [];

        $item = $this->toFluent($id);
        $keys = array_keys($this->items);
        if ($this->lookForChild($item, $after)) {
            return $item;
        }
        $position = array_search($after, $keys);

        if ($position === false) {
            return $this->addParent($id);
        }

        foreach ($keys as $key => $fluent) {
            $items[$fluent] = $this->items[$fluent];

            if ($key === $position) {
                $items[$id] = $item;
            }
        }

        $this->items = $items;

        return $item;
    }

    /**
     * does menu item should be placed before or after child item
     * 
     * @param Fluent $item
     * @param String $after
     * @return boolean
     */
    protected function lookForChild($item, $after)
    {
        if (str_contains($after, '.')) {
            $found = [];
            $this->lookInDeep($this->items, $after, $item, $found);
            if (!empty($found)) {
                $this->items[key($found)]->childs = current($found);
                return $item;
            }
        }
        return false;
    }

    /**
     * looking for matched item in nesty array configuration
     * 
     * @param array $elements
     * @param Strng $after
     * @param Fluent $item
     * @param array $buffer
     * @param array $return
     * @param String $current
     * @return array
     */
    protected function lookInDeep(array $elements, $after, $item, &$buffer = [], &$return = '', $current = null)
    {
        $branch = array();

        foreach ($elements as $key => $element) {
            $return = strlen($return) <= 0 ? $current : $return . '.' . $current;
            if ($return . '.' . $key == $after) {
                $this->buffer($buffer, $elements, $item, $after, $current);
            }
            if ($element instanceof Fluent) {
                $element = $element->toArray();
            }
            if (!empty($element['childs'])) {
                $children = $this->lookInDeep($element['childs'], $after, $item, $buffer, $return, $key);
                if ($children) {
                    $element[$key]['childs'] = $children;
                }
            } else {
                $return = '';
            }

            $branch[$key] = $element;
        }

        return $branch;
    }

    /**
     * buffer item into container
     * 
     * @param array $buffer
     * @param array $elements
     * @param Fluent $item
     * @param String $after
     * @param String $current
     * @return array
     */
    protected function buffer(&$buffer, $elements, $item, $after, $current)
    {
        foreach ($elements as $keyname => $element) {
            $found = last(explode('.', $after));
            if ($keyname == $found) {
                $buffer[$current][$keyname]  = $element;
                $buffer[$current][$item->id] = $item;
            } else {
                $buffer[$current][$keyname] = $element;
            }
        }
        return $buffer;
    }

    /**
     * Add item as child of $parent.
     *
     * @param  string  $id
     * @param  string  $parent
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function addChild($id, $parent)
    {
        $node = $this->descendants($this->items, $parent);

        if (!isset($node)) {
            return;
        }

        $item      = $node->get('childs');
        $item[$id] = $this->toFluent($id);

        $node->childs($item);

        return $item[$id];
    }

    /**
     * Add item as parent.
     *
     * @param  string  $id
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function addParent($id)
    {
        return $this->items[$id] = $this->toFluent($id);
    }

    /**
     * Add a new item, by prepend or append.
     *
     * @param  string  $id
     * @param  string  $location
     *
     * @return \Illuminate\Support\Fluent
     */
    public function add($id, $location = '#')
    {
        if ($location === '<' && count($keys = array_keys($this->items)) > 0) {
            return $this->addBefore($id, $keys[0]);
        } elseif (preg_match('/^(<|>|\^):(.+)$/', $location, $matches) && count($matches) >= 3) {
            return $this->pickTraverseFromMatchedExpression($id, $matches[1], $matches[2]);
        }
        return $this->addParent($id);
    }

    /**
     * Pick traverse from matched expression.
     *
     * @param  string  $id
     * @param  string  $key
     * @param  string  $location
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function pickTraverseFromMatchedExpression($id, $key, $location)
    {
        $matching = [
            '<' => 'addBefore',
            '>' => 'addAfter',
            '^' => 'addChild',
        ];

        $method = $matching[$key];
        return call_user_func([$this, $method], $id, $location);
    }

    /**
     * Check whether item by id exists.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function has($key)
    {
        $key = implode('.childs.', explode('.', $key));


        return !is_null(data_get($this->items, $key));
    }

    /**
     * Retrieve an item by id.
     *
     * @param  string  $key
     *
     * @return \Illuminate\Support\Fluent
     */
    public function is($key)
    {
        return $this->descendants($this->items, $key);
    }

    /**
     * Return all items.
     *
     * @return \Antares\Support\Collection
     */
    public function items()
    {
        return new Collection($this->items);
    }

}
