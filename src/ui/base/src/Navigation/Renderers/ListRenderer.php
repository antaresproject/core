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

namespace Antares\UI\Navigation\Renderers;

use Knp\Menu\ItemInterface;
use Knp\Menu\Renderer\ListRenderer as BaseListRenderer;

class ListRenderer extends BaseListRenderer {

    /**
     * @var string
     */
    protected static $dataNavId = 'data-nav-item-id';

    /**
     * @param ItemInterface $item
     * @param array $options
     * @return string
     */
    protected function renderItem(ItemInterface $item, array $options) {
        $item->setAttribute(self::$dataNavId,  $this->getNavigationItemId($item));

        return parent::renderItem($item, $options);
    }

    /**
     * @param ItemInterface $item
     * @return string
     */
    protected function getNavigationItemId(ItemInterface $item) : string {
        $id = $item->getName();

        if($item->getParent()) {
            return $this->getNavigationItemId($item->getParent()) . '.' . $id;
        }

        return $id;
    }

}