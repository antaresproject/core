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

use Antares\UI\Navigation\Events\MenuCreated;
use Antares\UI\Navigation\Renderers\BreadcrumbRenderer;
use Antares\UI\Navigation\Renderers\ListRenderer;
use Illuminate\Contracts\Events\Dispatcher;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Knp\Menu\Renderer\RendererInterface;

class Factory {

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var Matcher
     */
    protected $matcher;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var array
     */
    protected $renderOptions = [];

    /**
     * @var array|MenuItem[]
     */
    protected $menus = [];

    /**
     * Factory constructor.
     * @param MenuFactory $menuFactory
     * @param Matcher $matcher
     * @param Dispatcher $dispatcher
     * @param array $renderOptions
     */
    public function __construct(MenuFactory $menuFactory, Matcher $matcher, Dispatcher $dispatcher, array $renderOptions = []) {
        $this->menuFactory      = $menuFactory;
        $this->matcher          = $matcher;
        $this->dispatcher       = $dispatcher;
        $this->renderOptions    = $renderOptions;
    }

    /**
     * @return Menu
     */
    public function primaryMenu() : Menu {
        return $this->create('primary-menu', new ListRenderer($this->matcher, $this->renderOptions));
    }

    /**
     * @return Menu
     */
    public function secondaryMenu() : Menu {
        return $this->create('secondary-menu', new ListRenderer($this->matcher, $this->renderOptions));
    }

    /**
     * @return Menu
     */
    public function page() : Menu {
        return $this->create('page-menu', new ListRenderer($this->matcher, $this->renderOptions));
    }

    /**
     * @return Menu
     */
    public function breadcrumb() : Menu {
        return $this->create('breadcrumb', new BreadcrumbRenderer($this->matcher, $this->renderOptions));
    }

    /**
     * @param string $name
     * @param RendererInterface|null $renderer
     * @param array $options
     * @return Menu
     * @throws \InvalidArgumentException
     */
    public function of(string $name, RendererInterface $renderer = null, array $options = []) : Menu {
        $methods = get_class_methods($this);

        if( in_array($name, $methods, true) ) {
            throw new \InvalidArgumentException('The name is already reserved.');
        }

        return $this->create($name, $renderer, $options);
    }

    /**
     * @param string $name
     * @param RendererInterface|null $renderer
     * @param array $options
     * @return Menu
     */
    protected function create(string $name, RendererInterface $renderer = null, array $options = []) : Menu {
        if (!array_key_exists($name, $this->menus)) {
            $renderer = $renderer ?: new ListRenderer($this->matcher, $this->renderOptions);
            $menuItem = $this->menuFactory->createItem($name, $options);
            $menu = new Menu($menuItem, $this->dispatcher, $renderer);

            $this->dispatcher->dispatch(new MenuCreated($menu));

            $this->menus[$name] = $menu;
        }

        return $this->menus[$name];
    }

}