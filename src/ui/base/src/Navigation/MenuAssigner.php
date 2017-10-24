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

namespace Antares\UI\Navigation;

use Antares\UI\Navigation\Events\ItemAdded;
use Antares\UI\Navigation\Events\ItemAdding;
use Antares\UI\Navigation\Events\MenuCreated;
use Illuminate\Contracts\Events\Dispatcher;
use Closure;
use App;

class MenuAssigner {

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * MenuAssigner constructor.
     * @param Factory $factory
     * @param Dispatcher $dispatcher
     */
    public function __construct(Factory $factory, Dispatcher $dispatcher) {
        $this->factory      = $factory;
        $this->dispatcher   = $dispatcher;
    }

    /**
     * @param string $id
     * @param $callback
     */
    public function on(string $id, $callback) : void {
        $this->validate($callback);

        //$this->dispatcher->listen()
    }

    /**
     * @param string $id
     * @param $callback
     */
    public function before(string $id, $callback) : void {
        $this->validate($callback);

        $this->dispatcher->listen(ItemAdding::class, function(ItemAdding $event) use($id, $callback) {
            if($event->id === $id) {
                $this->invokeCallback($callback, $event->menu);
            }
        });
    }

    /**
     * @param $callback
     */
    public function primary($callback) : void {
        $this->validate($callback);

        $this->dispatcher->listen(MenuCreated::class, function(MenuCreated $event) use($callback) {
            if($event->menu->getMenuItem()->getName() === 'primary-menu') {
                $this->invokeCallback($callback,  $event->menu);
            }
        });
    }

    /**
     * @param $callback
     */
    public function secondary($callback) : void {
        $this->validate($callback);

        $this->dispatcher->listen(MenuCreated::class, function(MenuCreated $event) use($callback) {
            if($event->menu->getMenuItem()->getName() === 'secondary-menu') {
                $this->invokeCallback($callback,  $event->menu);
            }
        });
    }

    /**
     * @param string $id
     * @param $callback
     */
    public function after(string $id, $callback) : void {
        $this->validate($callback);

        $this->dispatcher->listen(ItemAdded::class, function(ItemAdded $event) use($id, $callback) {
            if($event->id === $id) {
                $this->invokeCallback($callback,  $event->menu);
            }
        });
    }

    /**
     * @param $callback
     */
    protected function validate($callback) : void {
        if( ! ($callback instanceof Closure || is_string($callback))) {
            throw new \InvalidArgumentException('The callback is invalid.');
        }
    }

    /**
     * @param $callback
     * @param Menu $menu
     */
    protected function invokeCallback($callback, Menu $menu) {
        if( is_string($callback) ) {
            $class = App::make($callback);

            if(method_exists($class, 'handle')) {
                $callback = [$class, 'handle'];
            }
        }

        call_user_func_array($callback, [$menu]);
    }
}