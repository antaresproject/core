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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\Navigation;

use Antares\UI\Navigation\Events\ItemAdding;
use Antares\UI\Navigation\Events\ItemAdded;
use Illuminate\Contracts\Events\Dispatcher;
use Knp\Menu\Renderer\RendererInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Knp\Menu\ItemInterface;

class Menu
{

    /**
     * @var ItemInterface
     */
    protected $menuItem;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * Menu constructor.
     * @param ItemInterface $menuItem
     * @param Dispatcher $dispatcher
     * @param RendererInterface $renderer
     */
    public function __construct(ItemInterface $menuItem, Dispatcher $dispatcher, RendererInterface $renderer)
    {

        $this->menuItem   = $menuItem;
        $this->dispatcher = $dispatcher;
        $this->renderer   = $renderer;
    }

    /**
     * Returns menu item instance.
     *
     * @return ItemInterface
     */
    public function getMenuItem(): ItemInterface
    {
        return $this->menuItem;
    }

    /**
     * Add a new item to the menu.
     *
     * @param string $id
     * @param string $label
     * @param null|string $uri
     * @param null|string $icon
     * @param array $attributes
     * @return Menu
     */
    public function addItem(string $id, string $label, string $uri = null, string $icon = null, array $attributes = []): Menu
    {
        $attributes = Arr::except($attributes, ['label', 'uri', 'icon']);

        $this->dispatcher->dispatch(new ItemAdding($id, $this->generateMenuItem($this->menuItem)));

        if ($icon && Str::startsWith($icon, 'zmdi-')) {
            $icon = 'zmdi ' . $icon;
        }

        if ($icon) {
            $label = sprintf('<i class="%s"></i> %s', $icon, $label);
        }

        $item = $this->menuItem->addChild($id, array_merge($attributes, [
            'label' => $label,
            'uri'   => $uri,
            'icon'  => $icon,
        ]));

        $item->setExtra('safe_label', true);

        $this->dispatcher->dispatch(new ItemAdded($id, $this->generateMenuItem($this->menuItem)));

        return $this;
    }

    /**
     * Returns submenu by the given item ID.
     *
     * @param string $id
     * @return Menu|null
     */
    public function getChild(string $id)
    {
        $menuItem = $this->menuItem->getChild($id);

        return $menuItem ? $this->generateMenuItem($menuItem) : null;
    }

    /**
     * Removes submenu by the given ID.
     *
     * @param string $id
     */
    public function forget(string $id): void
    {
        $this->menuItem->removeChild($id);
    }

    /**
     * Returns menu item wrapped by menu object.
     *
     * @param ItemInterface $menuItem
     * @return Menu
     */
    protected function generateMenuItem(ItemInterface $menuItem): Menu
    {
        return new Menu($menuItem, $this->dispatcher, $this->renderer);
    }

    /**
     * Renders menu.
     *
     * @param array $options
     * @return string
     */
    public function render(array $options = []): string
    {
        ;
        return $this->renderer->render($this->menuItem, $options);
    }

}
