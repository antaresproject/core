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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Service;

use Antares\UI\UIComponents\Repository\Repository;

class Service
{

    /**
     * Collection of ui components
     *
     * @var \Illuminate\Support\Collection
     */
    protected $components;

    /**
     * Constructing
     * 
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->components = $repository->load();
    }

    /**
     * Finds ui component details
     * 
     * @param array $attributes
     * @param String $uri
     * @return array
     */
    public function findOne($attributes, $uri)
    {
        $where      = [
            'resource' => $uri,
            'name'     => snake_case(array_get($attributes, 'name'))
        ];
        $components = $this->components->filter(function($item) use($where) {
            return $item->resource == array_get($where, 'resource') && $item->name == array_get($where, 'name');
        });
        $component = $components->count() ? $components->first() : null;
        if (!is_null($component)) {
            return array_merge($component->data, ['id' => $component->id]);
        }
        return [];
    }

}
