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

namespace Antares\UI\Navigation\Renderers;

use Knp\Menu\ItemInterface;

class BreadcrumbRenderer extends ListRenderer
{

    /**
     * @param ItemInterface $item
     * @param array $attributes
     * @param array $options
     * @return string
     */
    protected function renderList(ItemInterface $item, array $attributes, array $options)
    {
        if ($item->getLevel() === 0) {
            $attributes['class'] = 'breadcrumbs';
        }

        $shouldBeSubmenu = $this->shouldBeSubmenu($item);

        if ($shouldBeSubmenu) {
            $attributes['class'] = 'ddown__menu';

            foreach ($item->getChildren() as $child) {
                $child->setLinkAttribute('class', 'mdl-js-button mdl-js-ripple-effect');
                $child->setLabelAttribute('class', 'mdl-js-button mdl-js-ripple-effect');
            }
        }

        $html = parent::renderList($item, $attributes, $options);

        if ($shouldBeSubmenu) {
            return view()->make('antares/foundation::layouts.antares.partials._breadcrumbs_submenu', [
                        'label'   => $item->getLabel(),
                        'uri'     => $item->getUri(),
                        'submenu' => $html,
            ]);
        }

        return $html;
    }

    /**
     * @param ItemInterface $item
     * @param array $options
     * @return string
     */
    protected function renderLink(ItemInterface $item, array $options = array())
    {
        if ($this->shouldBeSubmenu($item)) {
            return '';
        }

        return $item->getExtra('invisible') ? '' : parent::renderLink($item, $options);
    }

    /**
     * @param ItemInterface $item
     * @return bool
     */
    protected function shouldBeSubmenu(ItemInterface $item): bool
    {
        return $item->getLevel() === 1 && $item->hasChildren();
    }

}
