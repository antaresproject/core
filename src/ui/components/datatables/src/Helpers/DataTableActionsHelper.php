<?php

namespace Antares\Datatables\Helpers;

use Antares\Support\Expression;
use HTML;
use Illuminate\Support\Arr;

class DataTableActionsHelper
{

    /**
     * Actions items.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * @return DataTableActionsHelper
     */
    public static function make(): DataTableActionsHelper
    {
        return new static;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->actions) === 0;
    }

    /**
     * @param string $url
     * @param string $label
     * @param array $attributes
     */
    public function addAction(string $url, string $label, array $attributes = [])
    {
        $this->actions[] = compact('url', 'label', 'attributes');
    }

    /**
     * @param string $label
     * @param DataTableActionsHelper $submenu
     * @param array $attributes
     */
    public function addSubmenu(string $label, DataTableActionsHelper $submenu, array $attributes = [])
    {
        $this->actions[] = compact('submenu', 'label', 'attributes');
    }

    /**
     * @param string $url
     * @param string $label
     * @param array $attributes
     */
    public function addEditAction(string $url, string $label, array $attributes = [])
    {
        $attributes = array_merge([
            'data-icon' => 'edit',
                ], $attributes);

        $this->actions[] = compact('url', 'label', 'attributes');
    }

    /**
     * @param string $url
     * @param string $label
     * @param array $attributes
     */
    public function addDeleteAction(string $url, string $label, array $attributes = [])
    {
        $attributes = array_merge([
            'data-icon'        => 'delete',
            'class'            => 'triggerable confirm',
            'data-http-method' => 'DELETE',
                ], $attributes);

        $this->actions[] = compact('url', 'label', 'attributes');
    }

    /**
     * @return Expression|string
     */
    public function buildList(): Expression
    {
        if (count($this->actions) === 0) {
            return '';
        }

        $items = array_map(function(array $item) {
            $submenu = Arr::get($item, 'submenu');

            if ($submenu instanceof DataTableActionsHelper) {
                $items[] = HTML::link('#', $item['label'], $item['attributes']);
                $items[] = $submenu->buildList();

                return HTML::create('li', HTML::raw(implode('', $items)));
            }

            $item = HTML::link($item['url'], $item['label'], $item['attributes']);

            return HTML::create('li', $item);
        }, $this->actions);
        ;

        return HTML::create('ul', HTML::raw(implode('', $items)));
    }

    /**
     * @param $entityId
     * @param array $attributes
     * @return string
     */
    public function build($entityId, array $attributes = []): string
    {
        if (count($this->actions) === 0) {
            return '';
        }

        $list = $this->buildList();

        $defaultAttributes = [
            'class'   => 'mass-actions-menu cm-actions',
            'data-id' => $entityId
        ];

        $attributes = array_merge($defaultAttributes, $attributes);
        $section    = HTML::create('div', HTML::create('section', $list), $attributes)->get();

        return '<i class="zmdi zmdi-more"></i>' . HTML::raw($section)->get();
    }

}
