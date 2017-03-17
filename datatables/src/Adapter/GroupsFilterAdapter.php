<?php

namespace Antares\Datatables\Adapter;

class GroupsFilterAdapter
{

    /**
     * Session key name preifx
     *
     * @var String 
     */
    protected static $prefix = 'filter';
    protected $name;

    public function apply($builder)
    {
        $this->saveRequestedSessionKey();
        $this->applyWhere($builder);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function saveRequestedSessionKey()
    {
        if (is_null($columns = ajax('columns'))) {
            return false;
        }
        $value = collect($columns)->first(function($key, $item) {
            $value = array_get($item, 'search.value');
            return !is_null($value) && $value !== "";
        });
        return $this->setSessionKeyValue($value);
    }

    protected function getSessionKey()
    {
        return implode('-', [self::$prefix, str_slug($this->name)]);
    }

    protected function setSessionKeyValue($value)
    {
        if (empty($value)) {
            return false;
        }
        $session = request()->session();
        $key     = $this->getSessionKey();

        if ($session->has($key)) {
            $session->forget($key);
        }
        $session->put($key, $value);
        $session->save();
    }

    public function getSessionValue()
    {
        $session = request()->session();
        $key     = $this->getSessionKey();

        return $session->has($key) ? $session->get($key) : null;
    }

    public function getSelected()
    {
        $column = $this->getSessionValue();

        return is_null($column) ? null : array_get($column, 'search.value');
    }

    protected function applyWhere(&$builder)
    {
        /* @var $query \Illuminate\Database\Query\Builder */
        if (!$builder instanceof \Illuminate\Database\Eloquent\Builder) {
            return;
        }
        $query  = $builder->getQuery();
        $wheres = (array) $query->wheres;
        $column = $this->getSessionValue();

        if (is_null($column)) {
            return false;
        }
        $columnName       = array_get($column, 'data');
        $found            = [];
        $subqueryBindings = [];

        foreach ($wheres as $index => $where) {
            /* @var $subquery \Illuminate\Database\Query\Builder */
            if (is_null($subquery = array_get($where, 'query'))) {
                continue;
            }
            foreach ($subquery->wheres as $subindex => $params) {
                if (is_null($whereColumn = array_get($params, 'column'))) {
                    continue;
                }
                if ($whereColumn == $columnName) {
                    $bindings = $subquery->getBindings();
                    unset($bindings[$subindex]);
                    $subquery->setBindings($bindings);
                    unset($subquery->wheres[$subindex]);
                    continue;
                }
            }
            $subqueryBindings = array_merge($subqueryBindings, $subquery->getBindings());

            if (empty($where['query']->wheres)) {
                continue;
            }
            $found[] = $where;
        }
        $query->setBindings(array_filter($subqueryBindings));
        $query->wheres = $found;
        return $builder->where($columnName, array_get($column, 'search.value'));
    }

    public function scripts($id, $columnIndex)
    {
        $js     = <<<EOD
           $(document).ready(function () {
                $('%s', document).on('change', function (e) {
                    
                    var table = $(this).closest('.tbl-c').find('[data-table-init]');
                    if (table.length < 0) {
                        return false;
                    }
                    var api = table.dataTable().api();
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    api.column(%d).search(val, false, false).draw();
                });
           }); 
EOD;
        $script = sprintf($js, '#' . $id, $columnIndex);
        return app('antares.asset')->container('antares/foundation::scripts')->inlineScript($id, $script);
    }

}
