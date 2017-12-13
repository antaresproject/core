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

use Knp\Menu\Renderer\RendererInterface;
use Knp\Menu\Iterator\RecursiveItemIterator;
use Knp\Menu\Matcher\MatcherInterface;
use RecursiveIteratorIterator;
use Knp\Menu\ItemInterface;

class BreadcrumbJsonRenderer implements RendererInterface
{

    private $matcher;
    private $defaultOptions;

    /**
     * {@inheritdoc}
     */
    public function __construct(MatcherInterface $matcher, array $defaultOptions = array())
    {
        $this->matcher        = $matcher;
        $this->defaultOptions = array_merge(array(
            'depth'             => null,
            'matchingDepth'     => null,
            'currentAsLink'     => true,
            'currentClass'      => 'current',
            'ancestorClass'     => 'current_ancestor',
            'firstClass'        => 'first',
            'lastClass'         => 'last',
            'compressed'        => false,
            'allow_safe_labels' => false,
            'clear_matcher'     => true,
            'leaf_class'        => null,
            'branch_class'      => null,
                ), $defaultOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function render(ItemInterface $item, array $options = array())
    {
        $options      = array_merge($this->defaultOptions, $options);
        $itemIterator = new RecursiveItemIterator($item);
        $iterator     = new RecursiveIteratorIterator($itemIterator, RecursiveIteratorIterator::SELF_FIRST);

        $items  = [];
        $parent = [
            'id'           => strtolower($item->getName()),
            'name'         => $item->getLabel(),
            'uri'          => $item->getUri(),
            'has_children' => $item->hasChildren(),
            'icon'         => $item->getAttribute('icon'),
            'parent'       => !is_null($item->getParent()) ? strtolower($item->getParent()->getName()) : ''
        ];
        foreach ($iterator as $item) {
            $translatedLabel = $item->getLabel();
            $id              = $item->getName();
            $parentId        = $item->getParent()->getName();
            $itemData        = ['id' => strtolower($item->getName()), 'name' => $translatedLabel, 'uri' => $item->getUri()];
            if ($parentId !== $id) {
                $itemData['parent'] = strtolower($parentId);
            }
            $itemData['has_children'] = $item->hasChildren();
            $itemData['icon']         = $item->getAttribute('icon');
            $items[]                  = $itemData;
        }
        $lastItem                     = count($items) - 1;
        $items[$lastItem]['lastItem'] = true;
        if ($parent['has_children']) {
            $parent['children'] = $items;
        }

        return json_encode($parent);
    }

}
