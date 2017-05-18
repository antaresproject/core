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

namespace Antares\Datatables\Html;

use Antares\Datatables\Contracts\DatatabledContract;
use Antares\Datatables\Adapter\ColumnFilterAdapter;
use Antares\Datatables\Adapter\GroupsFilterAdapter;
use Yajra\Datatables\Html\Builder as BaseBuilder;
use Antares\Datatables\Adapter\FilterAdapter;
use Antares\Datatables\Adapter\OrderAdapter;
use Illuminate\Contracts\Config\Repository;
use Antares\Asset\JavaScriptExpression;
use Antares\Asset\JavaScriptDecorator;
use Illuminate\Contracts\View\Factory;
use Antares\Html\Support\FormBuilder;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Events\Dispatcher;
use Yajra\Datatables\Html\Column;
use Antares\Support\Facades\Form;
use Antares\Support\Expression;
use Illuminate\Routing\Router;
use Antares\Html\HtmlBuilder;

class Builder extends BaseBuilder
{

    /**
     * defered data
     *
     * @var array
     */
    protected $deferredData = [];

    /**
     * whether use table global search
     *
     * @var boolean 
     */
    protected $searchable = true;

    /**
     * whether use table mass actions
     * 
     * @var boolean 
     */
    protected $massable = true;

    /**
     * whether table is groupable
     *
     * @var boolean 
     */
    protected $groupable = false;

    /**
     * mass actions for datatable instance
     *
     * @var array
     */
    protected $massActions = [];

    /**
     * datatable setter
     *
     * @var \Antares\Datatables\Services\DataTable
     */
    protected $datatable;

    /**
     * builder query
     *
     * @var mixed
     */
    protected $query;

    /**
     * Column filter adapter instance
     *
     * @var ColumnFilterAdapter 
     */
    protected $columnFilterAdapter;

    /**
     * Datatable behaviors container
     *
     * @var array 
     */
    protected $behaviors = [
        'column_filters' => false
    ];

    /**
     * Table attributes container
     *
     * @var array 
     */
    protected $tableAttributes = [
        'class'       => 'table dataTable billevo-table',
        'id'          => 'dataTableBuilder',
        'cellspacing' => '0',
        'width'       => '100%'
    ];

    /**
     * Table container attributes
     *
     * @var array 
     */
    protected $containerAttributes = [
        'class' => 'tbl-c',
    ];

    /** @var array * */
    protected $attributes = [
        "bFilter"        => true,
        'iDisplayLength' => 10,
        'bLengthChange'  => true,
        'bInfo'          => false,
        "columnDefs"     => [
        ],
        "serverSide"     => true,
        "dom"            => '<"dt-area-top"i>rt<"dt-area-bottom pagination pagination--type2" fpL><"clear">',
        "responsive"     => true,
        "bProcessing"    => false,
        "processing"     => false,
        "oLanguage"      => [
            "oPaginate"   => [
                "sPrevious" => "<i class='zmdi zmdi-long-arrow-left dt-pag-left'></i>",
                "sNext"     => "<i class='zmdi zmdi-long-arrow-right dt-pag-right'></i>",
            ],
            "sLengthMenu" => "_MENU_",
        ],
        'lengthMenu'     => [
            [10, 25, 50],
            [10, 25, 50]
        ],
    ];

    /**
     * filter adapter instance
     *
     * @var \Antares\Datatables\Adapter\FilterAdapter 
     */
    protected $filterAdapter;

    /**
     * datatable instance name
     *
     * @var String 
     */
    protected $name;

    /**
     * datatables fetch method
     *
     * @var String 
     */
    protected $method = 'POST';

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var Router
     */
    protected $router;

    /**
     * List of additional table selects
     *
     * @var array
     */
    protected $selects = [];

    /**
     * Right ctrls container
     *
     * @var String
     */
    protected $rightCtrls = null;

    /**
     * Whether scripts are disabled
     *
     * @var Boolean
     */
    protected $disabledScripts = false;

    /**
     * Constructing
     * 
     * @param Repository $config
     * @param Factory $view
     * @param HtmlBuilder $html
     * @param UrlGenerator $url
     * @param FormBuilder $form
     */
    public function __construct(Repository $config, Factory $view, HtmlBuilder $html, UrlGenerator $url, FormBuilder $form, FilterAdapter $filterAdapter, Router $router, Dispatcher $dispatcher)
    {
        $this->config                = $config;
        $this->view                  = $view;
        $this->html                  = $html;
        $this->url                   = $url;
        $this->collection            = new Collection;
        $this->form                  = $form;
        $this->filterAdapter         = $filterAdapter;
        $this->router                = $router;
        $this->dispatcher            = $dispatcher;
        $this->tableAttributes['id'] = $this->tableAttributes['id'] . str_random(3);
    }

    protected function getGlobalSearchValue()
    {

        if (request()->has('search') && !is_array($value = request()->get('search'))) {

            return $value;
        }
        return false;
    }

    /**
     * Get generated raw scripts.
     *
     * @return string
     */
    public function generateScripts()
    {

        app('antares.asset')->container('antares/foundation::application')
                ->add('gridstack', '/webpack/view_datatables.js', ['webpack_gridstack', 'app_cache'])
                ->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache']);


        $columns = $this->hasColumnFilter() ? $this->getColumnFilterAdapter()->getColumns()->toArray() : $this->collection->toArray();

        array_set($this->attributes, 'iDisplayLength', $this->datatable->getPerPage());
        $args         = array_merge(
                $this->attributes, ['ajax' => $this->ajax, 'columns' => $columns]
        );
        $searching    = (($value        = $this->getGlobalSearchValue()) !== false) ? "data.search.value=instance.find('.mdl-textfield__input').val();" : '';
        $groupsFilter = app(\Antares\Datatables\Adapter\GroupsFilterAdapter::class);
        $sessionValue = $groupsFilter->getSessionValue();
        $cols         = '';
        if ($sessionValue && !ajax()) {
            $columns = [];
            foreach ($this->collection as $columnIndex => $column) {


                $value       = '';
                $regex       = false;
                $fromSession = array_get($sessionValue, $columnIndex, $sessionValue);

                if ($column->data == array_get($fromSession, 'data') && $column->name == array_get($fromSession, 'name')) {

                    $value = array_get($fromSession, 'search.value');
                    $regex = true;
                }


                $columns[] = [
                    'data'       => $column->data,
                    'name'       => $column->name,
                    'searchable' => $column->searchable,
                    'orderable'  => $column->orderable,
                    'search'     => [
                        'value' => $value,
                        'regex' => $regex
                    ],
                ];
            }
            $cols = 'data.columns = ' . JavaScriptDecorator::decorate($columns) . ';';
        }

        $eventAfterSearch = (request()->has('search') && !request()->ajax()) ? '$(document).trigger( "datatables.searchLoaded", [ dtInstance,data,' . count(config('search.datatables')) . ' ] );' : '';
        $ajax             = <<<EOD
            function (data, callback, settings) {                        
                    if(data.draw===1){
                        $cols
                    }
                    var dtInstance=$(settings.oInstance);
                    var instance = dtInstance.closest('.grid-stack-item-content').length>0?dtInstance.closest('.grid-stack-item-content'):dtInstance.closest('.tbl-c');                    
                    $searching
                    if (instance.length > 0) {
                        instance.LoadingOverlay('show');
                    }
                    settings.jqXHR = $.ajax({
                        "dataType": 'json',
                        "timeout": 20000,
                        "type": "%s",
                        "url": "%s",
                        "data": data,
                        "success": callback
                    }).always(function (data) {
                        $eventAfterSearch
                        if (instance.length > 0) {
                            instance.LoadingOverlay('hide');
                        }                 
                        $(document).trigger( "datatablesLoaded", [ dtInstance ] );
                    });                    
            }
EOD;


        $url          = $this->getTargetUrl();
        $args['ajax'] = new JavaScriptExpression(sprintf($ajax, $this->method, $url));
        $id           = $this->tableAttributes['id'];
        if (isset($args['fnRowCallback'])) {
            $args['fnRowCallback'] = new JavaScriptExpression($args['fnRowCallback']);
        }
        $args['fnRowCallback']  = new JavaScriptExpression("function( nRow, aData, iDisplayIndex, iDisplayIndexFull ){
                
                if($(nRow).find('.dt-actions').length<=0){
                    $(nRow).addClass('no-actions');
                }
            }");
        $args['fnDrawCallback'] = new JavaScriptExpression("function (oSettings) {
                var tblC=this.closest('.tbl-c');
                if ( this.fnGetData().length === 0 ) {                    
                    tblC.addClass('tbl-c--zd');
                }else{
                   tblC.removeClass('tbl-c--zd');  
                }
            }");

        $args['initComplete'] = new JavaScriptExpression(isset($args['initComplete']) ? $args['initComplete'] : 'function () {}');
        $args['columnDefs'][] = new JavaScriptExpression('{ responsivePriority:0, targets: -1 }');

        $parameters = JavaScriptDecorator::decorate($args);

        $variables = JavaScriptDecorator::decorate([
                    'table' => new JavaScriptExpression('$("#' . $id . '")'),
                    'tr'    => new JavaScriptExpression("$('#" . $id . " tr')")
        ]);


        $javascript   = file_get_contents(sandbox_path('packages/core/js/datatables.js'));
        $instanceName = 'dataTable' . str_random(3);
        $oTable       = 'oTable' . str_random(3);


        $javascript = str_replace('inject.id', "'#$id'", $javascript);
        $javascript = str_replace('instance', $instanceName, $javascript);
        $javascript = str_replace('oTable', $oTable, $javascript);
        $javascript = str_replace('inject.variables', $variables, $javascript);
        $javascript = str_replace('inject.options', $parameters, $javascript);
        $javascript = str_replace('inject.contextMenu.build', $parameters, $javascript);
        return $javascript;
    }

    protected function getTargetUrl()
    {
        if ($this->ajax) {
            return $this->ajax;
        }

        $action = $this->router->getCurrentRoute()->getAction();

        list($controller, $method) = explode('@', $action['controller']);

        if (is_subclass_of($controller, DatatabledContract::class)) {
            return $this->url->action('\\' . $controller . '@datatable');
        }

        return $this->url->current();
    }

    /**
     * groupable setter
     * 
     * @param boolean $groupable
     * @return \Antares\Datatables\Html\Builder
     */
    public function groupable($groupable)
    {
        $this->groupable = $groupable;
        return $this;
    }

    /**
     * global search setter
     * 
     * @param boolean $searchable
     * @return Builder
     */
    public function searchable($searchable)
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * mass actions setter
     * 
     * @param boolean $massable
     * @return Builder
     */
    public function massable($massable)
    {
        $this->massable = $massable;
        return $this;
    }

    /**
     * Generate DataTable's table html.
     *
     * @param  array $attributes
     * @param boolean $drawFooter
     * @return string
     */
    public function table(array $attributes = [], $drawFooter = false)
    {
        if (count($this->deferredData)) {
            return $this->tableDeferred($attributes);
        }

        $this->tableAttributes = $attributes ?: $this->tableAttributes;
        $scrollable            = '';
        if ($this->datatable->count() > 10 or strlen($this->ajax) > 0) {
            $scrollable = 'data-scrollable';
        }

        return $this->tableInit() . $this->beforeTable() . '<table ' . $scrollable . ' data-table-init = "true" ' . $this->html->attributes($this->tableAttributes) . '></table>' . $this->afterTable();
    }

    /**
     * triggers before table
     * 
     * @return string
     */
    protected function beforeTable()
    {
        $hasMassActions  = $this->hasMassActions();
        $filters         = $this->filterAdapter->getFilters();
        $hasColumnFilter = $this->hasColumnFilter();

        if (!$hasMassActions and ! $this->searchable and empty($this->selects) and ! $filters and ! $hasColumnFilter) {
            return '';
        }
        $return = '<div class="card-ctrls mt24"><div class="card-ctrls__left">';

        if (!empty($this->selects)) {
            $return .= implode('', $this->selects);
        }

        if ($this->searchable) {
            $value  = (($value  = $this->getGlobalSearchValue()) !== false) ? $value : '';
            $return .= '<div class="search-box search-box--dark search-box--big ' . (!empty($this->selects) ? 'ml25' : '') . ' mr50">
                    <i class="zmdi zmdi-search"></i>
                    <form action="#">
                        <div class="search-box__mdl-textfield mdl-textfield mdl-js-textfield w260" >
                            <input class="mdl-textfield__input search-box__search-field" type="text" id="main-search" value="' . $value . '">
                            <label class="mdl-textfield__label" for="sample1">Search...</label>
                        </div>
                    </form>
                </div>';
        }
        $return .= '</div>';


        $result = event(strtolower(class_basename($this->datatable)) . '.datatables.top.center', [&$return]);
        $return .= isset($result[0]) ? $result[0] : '';

        $return .= '<div class="card-ctrls__right">';
        if (!is_null($this->rightCtrls)) {
            $return .= $this->rightCtrls;
        }
        if (empty($filters) and ! is_null($this->datatable)) {
            $list = $this->datatable->getFilters();
            foreach ($list as $filter) {
                $this->filterAdapter->add($filter);
            }
            $filters = $this->filterAdapter->getFilters();
        }
        if ($hasColumnFilter) {
            $return .= $this->getColumnFilterAdapter();
        }
        if ($filters) {
            $return .= '<div id="filter-save-url" data-url=' . handles('antares/foundation::datatables/filters/save') . '></div><div class="ddown ctrls__right ml10 ">' . $filters . '</div>';
        }
        if ($hasMassActions) {
            $return .= '
                <div class="ddown ddown--left">
                    <div class="ddown__init btn--dropdown btn btn--capitalize btn--md btn--primary mdl-js-button is-disabled ml8" id="table-ma" disabled>' . trans('antares/foundation::label.with_selected') . '</div>                    
                    <div class="ddown__content">
                    <div class="ddown__arrow"></div>
                    <ul class="ddown__menu">';
            foreach ($this->massActions as $massAction) {
                $return .= '<li>' . $massAction->get() . '</li>';
            }
            $return .= '</ul></div></div>';
        }
        return $return . '</div></div>';
    }

    /**
     * Adds right ctrls container
     * 
     * @param String $rightCtrls
     * @return $this
     */
    public function addRightCtrls($rightCtrls = null)
    {
        if (!is_string($rightCtrls)) {
            return $this;
        }
        $this->rightCtrls = $rightCtrls;
        return $this;
    }

    /**
     * Adds column filter adapter instance
     * 
     * @return $this
     */
    public function addColumnFilter()
    {
        array_set($this->behaviors, 'column_filters', true);
        return $this;
    }

    /**
     * Whether datatable has colum filter
     * 
     * @return boolean
     */
    protected function hasColumnFilter()
    {
        return array_get($this->behaviors, 'column_filters', false);
    }

    /**
     * Gets colum filter adapter instance
     * 
     * @return ColumnFilterAdapter
     */
    protected function getColumnFilterAdapter()
    {
        if (is_null($this->columnFilterAdapter)) {
            $this->columnFilterAdapter = app(ColumnFilterAdapter::class, [$this->collection, get_class($this->datatable)]);
        }
        return $this->columnFilterAdapter;
    }

    /**
     * whether datatable has mass actions
     * 
     * @return boolean
     */
    protected function hasMassActions()
    {
        return !empty($this->massActions) and $this->massable;
    }

    /**
     * add filter to datatables 
     * 
     * @param String $classname
     * @return \Antares\Datatables\Html\Builder
     */
    public function addFilter($classname, $query)
    {
        $filterClass = is_object($classname) ? get_class($classname) : $classname;
        if (!class_exists($filterClass)) {
            return $this;
        }
        $filter = str_slug(class_basename($filterClass));
        $path   = uri();
        $this->dispatcher->fire("datatables:filters.{$path}.{$filter}.before", [$this->filterAdapter, $query]);
        $this->filterAdapter->add($classname);
        $this->dispatcher->fire("datatables:filters.{$path}.{$filter}.after", [$this->filterAdapter, $query]);
        return $this;
    }

    /**
     * triggers after generates table
     * 
     * @return string
     */
    protected function afterTable()
    {
        return '</div>';
    }

    /**
     * defered data setter
     * 
     * @param array | mixed $data
     * @param mixed $count
     * @return Builder
     */
    public function setDeferedData()
    {
        if (request()->has('search')) {
            return $this;
        }

        $totalItemsCount    = $this->datatable->count();
        $this->deferredData = $this->datatable->ajax()->getData()->data;
        $filters            = $this->datatable->getFilters();
        $query              = $this->datatable->query();
        foreach ($filters as $filter) {
            $this->addFilter($filter, $query);
        }

        $this->attributes = array_merge($this->attributes, [
            "deferLoading"   => $totalItemsCount,
            'iDisplayLength' => $this->datatable->getPerPage()
        ]);
        return $this;
    }

    /**
     * Generate DataTable's table html.
     *
     * @param array $attributes
     * @return string
     */
    public function tableDeferred(array $attributes = [])
    {
        $string = '<thead><tr>';
        foreach ($this->collection as $collectedItem) {
            $columnAttributes          = array_only($collectedItem->toArray(), ['class', 'id', 'width', 'style', 'data-class', 'data-hide']);
            $columnAttributes['class'] = (isset($collectedItem->bolded) and $collectedItem->bolded === true) ? 'bolded' : array_get($columnAttributes, 'class', '');
            $params                    = $this->html->attributes($columnAttributes);
            $string                    .= "<th " . $params . ">{$collectedItem->title}</th>";
        }
        $string .= '</tr></thead>';
        $string .= '<tbody>';
        foreach ($this->getDeferredDataItems() as $item) {
            $string .= '<tr>';
            foreach ($this->collection as $collectedItem) {
                $value  = isset($item->{$collectedItem->data}) ? $item->{$collectedItem->data} : '---';
                $string .= "<td>{$value}</td>";
            }
            $string .= '</tr>';
        }
        $string                .= '</tbody>';
        $this->tableAttributes = array_merge($this->tableAttributes, $attributes);
        $scrollable            = '';
        if ($this->datatable->count() > 10 or strlen($this->ajax) > 0) {
            $scrollable = 'data-scrollable';
        }
        $massable = (int) $this->hasMassActions();
        return $this->tableInit() . $this->beforeTable() . '<table data-massable="' . $massable . '" ' . $scrollable . ' data-table-init = "true" ' . $this->html->attributes($this->tableAttributes) . '>' . $string . '</table>' . $this->afterTable();
    }

    /**
     * Disable javascript scripts
     * 
     * @return $this
     */
    public function disableScripts()
    {
        $this->disabledScripts = true;
        return $this;
    }

    /**
     * Inits table container
     * 
     * @return String
     */
    protected function tableInit(): String
    {
        $attributes = $this->html->attributes($this->containerAttributes);
        return "<div {$attributes}>";
    }

    /**
     * @return array
     */
    protected function getDeferredDataItems()
    {
        if ($this->deferredData instanceof JsonResponse) {
            return $this->deferredData->getData()->data;
        }

        return $this->deferredData;
    }

    /**
     * Container attributes setter
     * 
     * @param array $attributes
     * @return $this
     */
    public function containerAttributes(array $attributes = []): Builder
    {
        foreach ($this->containerAttributes as $name => $value) {
            if (is_null($param = array_get($attributes, $name))) {
                continue;
            }
            array_set($this->containerAttributes, $name, $value . ' ' . $param);
        }
        return $this;
    }

    /**
     * Table attributes setter
     * 
     * @param array $attributes
     * @return $this
     */
    public function tableAttributes(array $attributes = array()): Builder
    {
        array_set($attributes, 'id', array_get($attributes, 'id') . str_random(3));
        $this->tableAttributes = array_merge($this->tableAttributes, $attributes);
        return $this;
    }

    /**
     * Add a column in collection using attributes.
     *
     * @param  array $attributes
     * @return $this
     */
    public function addColumn(array $attributes)
    {
        $query  = $this->getQuery();
        $orders = $query instanceof \Illuminate\Database\Eloquent\Builder ? $query->getQuery()->orders : null;
        if (!isset($this->attributes['order']) and ! is_null($query) and ! is_null($orders)) {
            foreach ($orders as $order) {
                if ($order['column'] != $attributes['name']) {
                    continue;
                }
                $this->attributes['order'] = [
                    $this->collection->count(), $order['direction']
                ];
            }
        }
        $path = uri();
        $this->dispatcher->fire('datatables:' . $path . ':column.' . $attributes['name'], [&$attributes]);
        $this->collection->push(new Column($attributes));
        $this->dispatcher->fire('datatables:' . $path . ':after.' . $attributes['name'], $this);
        return $this;
    }

    /**
     * datatable name setter
     * 
     * @param String $name
     * @return \Antares\Datatables\Html\Builder
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * filters adapter getter
     * 
     * @return \Antares\Datatables\Adapter\FilterAdapter
     */
    public function getFilterAdapter()
    {
        return $this->filterAdapter;
    }

    /**
     * method setter
     * 
     * @param String $method
     * @return \Antares\Datatables\Html\Builder
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * appends mass actions to mass actions container
     * 
     * @param Expression $massAction
     * @return \Antares\Datatables\Html\Builder
     */
    public function addMassAction($name, Expression $massAction)
    {
        $model             = $this->getQuery()->getModel();
        $this->massActions = array_merge($this->massActions, (array) event('datatables:' . uri() . ':before.massactions.action.' . $name, [$this->massActions, $model], true));
        if (empty($this->massActions)) {
            $this->massActions = [];
        }
        array_push($this->massActions, $massAction);
        $this->massActions = array_merge($this->massActions, (array) event('datatables:' . uri() . ':after.massactions.action.' . $name, [$this->massActions, $model], true));
        $this->massActions = array_unique($this->massActions);
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function getRawData()
    {
        return $this->deferredData->getData();
    }

    /**
     * query builder setter
     * 
     * @param mixed $query
     * @return \Antares\Datatables\Html\Builder
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * query builder getter
     * 
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * defered data getter
     * 
     * @return mixed
     */
    public function getDeferedData()
    {
        return $this->deferredData;
    }

    /**
     * Defered data setter
     * 
     * @param type $datatable
     * @return \Antares\Datatables\Html\Builder
     */
    public function setDataTable($datatable)
    {
        $this->setQuery($datatable->query());
        $this->datatable = $datatable;
        return $this;
    }

    /**
     * Add additional table selects
     * 
     * @param String $html
     * @return \Antares\Datatables\Html\Builder
     */
    public function addGroupSelect($options, $columnIndex = 0, $defaultSelected = null, array $attributes = [])
    {
        $orderAdapter = app(OrderAdapter::class)->setClassname(get_class($this->datatable));
        if (($order        = $orderAdapter->getSelected()) !== false) {
            $this->parameters([
                'order' => [[$order['column'], $order['dir']]],
            ]);
        }
        $groupsFilter = app(GroupsFilterAdapter::class)->setClassname(get_class($this->datatable))->setIndex($columnIndex);
        $data         = ($options instanceof Collection) ? $options->toArray() : $options;


        $id    = array_get($this->tableAttributes, 'id') . '-filter-group';
        if (is_null($value = $groupsFilter->getSelected($columnIndex))) {
            $value = $defaultSelected;
        }
        $decorated = $this->html->decorate($attributes, [
            'data-selectAR--mdl-big' => "true",
            'class'                  => 'select2--prefix',
            'id'                     => $id
        ]);

        $html = Form::select('category', $data, $value, $decorated);
        $groupsFilter->scripts(array_get($decorated, 'id'), $columnIndex);
        array_push($this->selects, $html);
        return $this;
    }

    /**
     * Configure DataTable's parameters.
     *
     * @param  array $attributes
     * @return $this
     */
    public function parameters(array $attributes = [])
    {
        if (!is_null($defaultOrder = array_get($attributes, 'order'))) {
            $orderAdapter = app(OrderAdapter::class)->setClassname(get_class($this->datatable));
            if (($order        = $orderAdapter->getSelected()) !== false) {
                $attributes = array_merge($attributes, [
                    'order' => [[$order['column'], $order['dir']]],
                ]);
            }
        }



        return parent::parameters($attributes);
    }

    /**
     * Generate DataTable javascript.
     *
     * @param  null $script
     * @param  array $attributes
     * @return string
     */
    public function scripts($script = null, array $attributes = ['type' => 'text/javascript'])
    {
        if ($this->disabledScripts) {
            return false;
        }
        $container = app('antares.asset')->container('antares/foundation::scripts');
        $container->add('context_menu', '/packages/core/js/contextMenu.js');
        $script    = $script ?: $this->generateScripts();
        if (app('request')->ajax()) {
            return '<script' . $this->html->attributes($attributes) . '>' . $script . '</script>' . PHP_EOL;
        } else {
            return $container->inlineScript($this->tableAttributes['id'], $script);
        }
    }

}
