<?php

/**
 * Part of the Antares Project package.
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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Datatables;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Yajra\Datatables\Datatables as SupportDatatables;
use Antares\Datatables\Engines\QueryBuilderEngine;
use Antares\Datatables\Engines\CollectionEngine;
use Antares\Datatables\Engines\EloquentEngine;
use Illuminate\Support\Collection;

class Datatables extends SupportDatatables
{

    /**
     * Datatables request object.
     *
     * @var \Yajra\Datatables\Request
     */
    public $request;

    /**
     * Datatables constructor.
     *
     * @param \Antares\Datatables\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request->request->count() ? $request : Request::capture();
    }

    /**
     * Gets query and returns instance of class.
     *
     * @param  mixed $builder
     * @return mixed
     */
    public static function of($builder)
    {
        $datatables          = app(Datatables::class);
        $datatables->builder = $builder;

        if ($builder instanceof QueryBuilder) {
            $ins = $datatables->usingQueryBuilder($builder);
        } else {
            $ins = $builder instanceof Collection ? $datatables->usingCollection($builder) : $datatables->usingEloquent($builder);
        }

        return $ins;
    }

    /**
     * Datatables using Query Builder.
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @return \Yajra\Datatables\Engines\QueryBuilderEngine
     */
    public function usingQueryBuilder(QueryBuilder $builder)
    {
        return new QueryBuilderEngine($builder, $this->request);
    }

    /**
     * Datatables using Collection.
     *
     * @param \Illuminate\Support\Collection $builder
     * @return \Yajra\Datatables\Engines\CollectionEngine
     */
    public function usingCollection(Collection $builder)
    {
        return new CollectionEngine($builder, $this->request);
    }

    /**
     * Datatables using Eloquent.
     *
     * @param  mixed $builder
     * @return \Yajra\Datatables\Engines\EloquentEngine
     */
    public function usingEloquent($builder)
    {
        return new EloquentEngine($builder, $this->request);
    }

}
