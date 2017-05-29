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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Datatables\Services;

use Yajra\Datatables\Services\DataTable as BaseDataTableService;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Antares\Datatables\Session\PerPage;
use Illuminate\Support\Facades\Event;
use Antares\Datatables\Html\Builder;
use Antares\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Exception;

abstract class DataTable extends BaseDataTableService
{

    /**
     * Datatable filters
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Records per page
     *
     * @var int
     */
    public $perPage = 25;

    /**
     * table actions container
     *
     * @var array
     */
    protected $tableActions = [];

    /**
     * PerPage adapter instance
     *
     * @var PerPage 
     */
    protected $perPageAdapter = null;

    /**
     * Default quick search settings
     *
     * @var String
     */
    protected $search = [
        'view'     => 'antares/search::admin.partials._default_row',
        'category' => 'Foundation'
    ];

    /**
     * Construct
     *
     * @param Datatables       $datatables
     * @param ViewFactory $viewFactory
     * @param PerPage $perPageAdapter
     */
    public function __construct(Datatables $datatables, ViewFactory $viewFactory, PerPage $perPageAdapter = null)
    {
        $this->datatables     = $datatables;
        $this->viewFactory    = $viewFactory;
        $this->perPageAdapter = is_null($perPageAdapter) ? app(PerPage::class) : $perPageAdapter;
    }

    /**
     * add datatables action button
     *
     * @param String $action
     * @param mixed  $btn
     * @return Presenter
     */
    protected function addTableAction($action, $row, $btn)
    {
        if (empty($this->tableActions)) {
            $this->tableActions = new Collection();
        }
        $path = uri();
        Event::fire('datatables:' . $path . ':before.action.' . $action, [$this->tableActions, $row]);
        $this->tableActions->push($btn);
        Event::fire('datatables:' . $path . ':after.action.' . $action, [$this->tableActions, $row]);

        return $this;
    }

    /**
     * Datatables query engine
     */
    public abstract function query();

    /**
     * Counts current query.
     *
     * @return mixed
     */
    public function count()
    {
        $query   = $this->query();
        $myQuery = $query instanceof EloquentBuilder ? clone $this->query() : $query;
        if ($query instanceof Collection) {
            return $query->count();
        }

        $finalQuery = $this->applyScopes($myQuery);
        $this->applyGlobalGroupScope($finalQuery);


        // if its a normal query ( no union, having and distinct word )
        // replace the select with static text to improve performance
        $connection = $finalQuery->getQuery()->getConnection();

        if (!Str::contains(Str::lower($finalQuery->toSql()), ['union', 'having', 'distinct', 'order by', 'group by'])) {

            $row_count = $connection->getQueryGrammar()->wrap('row_count');
            return $connection->table($connection->raw('(' . $finalQuery->toSql() . ') count_row_table'))->select($connection->raw("'1' as {$row_count}"))->setBindings($finalQuery->getBindings())->count();
        }

        return $connection->table($connection->raw('(' . $finalQuery->toSql() . ') count_row_table'))
                        ->setBindings($finalQuery->getBindings())->count();
    }

    /**
     * Query getter for defer initialization
     *
     * @param boolean $applyGlobalGroupScope
     */
    protected function getQuery($applyGlobalGroupScope = true)
    {
        $query      = $this->query();
        $this->addFilters();
        $finalQuery = $this->applyScopes($query);
        if ($applyGlobalGroupScope) {
            $this->applyGlobalGroupScope($finalQuery);
        }
        $request = app('request');
        if ($finalQuery instanceof Collection) {
            return $finalQuery;
        }
        $perPage = $this->getPerPage();
        if (!$request->ajax()) {
            return $finalQuery->skip($request['start'])->take($perPage);
        }

        return $finalQuery;
    }

    /**
     * Gets perPage attribute
     * 
     * @return mixed
     */
    public function getPerPage()
    {
        return $this->perPageAdapter->get($this);
    }

    /**
     * renders datatables
     *
     * @param \Illuminate\View\View $view
     * @param array                 $data
     * @param array                 $mergeData
     * @return array
     */
    public function render($view, $data = [], $mergeData = [])
    {
        view()->share('content_class', 'side-menu');
        $this->addFilters();

        return parent::render($view, $data, $mergeData);
    }

    /**
     * add filters to datatable instance
     *
     * @return void
     */
    private function addFilters()
    {
        if (empty($this->filters)) {
            return;
        }
        foreach ($this->filters as $filter) {
            if (!class_exists($filter)) {
                continue;
            }
            $scope = app($filter);
            $this->addScope($scope);
        }

        return;
    }

    /**
     * filters getter
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * prepare datatable instance before
     *
     * @param EloquentBuilder|Collection $query
     * @return \Antares\Datatables\Engines\BaseEngine
     */
    public function prepare($query = null)
    {
        $of         = is_null($query) ? $this->getQuery() : $query;
        $datatables = $this->datatables->of($of, get_class($this));
        $path       = uri();
        Event::fire("datatables.value.{$path}", [$datatables]);
        return $datatables;
    }

    /**
     * datatable name setter
     *
     * @param String $name
     * @return Builder
     */
    public function setName($name)
    {
        return app(Builder::class)->setName($name)->setDataTable($this);
    }

    /**
     * row actions decorator
     *
     * @return String
     */
    public function decorateActions($row = null)
    {
        if (empty($this->tableActions)) {
            return '';
        }
        $html    = app('html');
        $section = $html->create('div', $html->raw(implode('', $this->tableActions->toArray())), ['class' => 'mass-actions-menu', 'data-id' => $row->id ? $row->id : ''])->get();

        return '<i class="zmdi zmdi-more"></i>' . app('html')->raw($section)->get();
    }

    /**
     * Apply query scopes.
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    protected function applyScopes($query)
    {
        foreach ($this->scopes as $scope) {
            $scope->apply($query);
        }
        return $query;
    }

    protected function applyGlobalGroupScope($query)
    {
        $query      = $this->getQuery(false);
        $datatables = $this->datatables->of($query);


        $groupsFilter = app(\Antares\Datatables\Adapter\GroupsFilterAdapter::class);
        $groupsFilter->setClassname(get_class($this));
        $groupsFilter->saveRequestedSessionKey();

        app(\Antares\Datatables\Adapter\OrderAdapter::class)->setClassname(get_class($this))->saveRequestedSessionKey();

        if (!isset($this->ajax)) {
            $groupsFilter->apply($query);
        }
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Gets quick search params
     * 
     * @return String
     */
    public function getQuickSearchRow(array $data = [])
    {
        $pattern = '';
        try {
            $pattern = $this->getPatternUrl();
        } catch (Exception $ex) {
            
        }
        $view = array_get($this->search, 'view');
        if (!view()->exists($view)) {
            return false;
        }
        $return = [
            'content'  => view($view, $data)->render(),
            'category' => array_get($this->search, 'category'),
        ];
        if (is_null($id     = array_get($data, 'id'))) {
            array_set($return, 'id', str_replace('{id}', $id, $pattern));
        }
        return $return;
    }

}
