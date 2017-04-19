<?php

namespace Antares\Datatables\Adapter;

use Closure;

class GroupsFilterAdapter
{

    /**
     * Session key name preifx
     *
     * @var String 
     */
    protected static $prefix = 'filter';

    /**
     *
     * @var type 
     */
    protected $classname;

    /**
     *
     * @var type 
     */
    protected $index;

    public function apply($builder)
    {
        $this->saveRequestedSessionKey();
    }

    public function setClassname($name)
    {
        $this->classname = strtolower(str_replace('\\', '_', $name));
        return $this;
    }

    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    public function setEngineInstance(&$engine)
    {
        $filters = array_get($engine->getColumnDef(), 'filter', []);
        $values  = $this->getSessionValue();
        if (!$values) {
            return $this;
        }
        foreach ($values as $value) {
            if (is_null($name = array_get($value, 'name'))) {
                continue;
            }
            if (!isset($filters[$name])) {
                continue;
            }
            if ($filters[$name]['method'] instanceof Closure) {
                $keyword    = array_get($value, 'search.value');
                $whereQuery = $engine->getQuery();
                request()->merge(['columns' => [$value]]);
                call_user_func_array($filters[$name]['method'], [$whereQuery, $keyword]);
            }
        }
        return $this;
    }

    public function saveRequestedSessionKey()
    {
        if (!($columns = ajax('columns'))) {
            return false;
        }
        $index = 0;
        $value = collect($columns)->first(function($item, $key) use(&$index) {
            $value = array_get($item, 'search.value');
            $index = $key;
            return (!is_null($value) && $value !== "");
        });
        return $this->setSessionKeyValue($value, $index);
    }

    protected function setSessionKeyValue($value, $index = 0)
    {
        if (empty($value)) {
            return false;
        }
        $session = session()->driver();

        if ($session->has($this->classname)) {
            $values = $session->get($this->classname);
            $session->forget($this->classname);
            if (isset($values[$index])) {
                unset($values[$index]);
            }
            $values[$index] = $value;
            $session->put($this->classname, $values);
        } else {
            $session->put($this->classname, [$index => $value]);
        }
        $session->save();
    }

    public function getSessionValue($columnIndex = null)
    {
        if (php_sapi_name() === 'cli') {
            return false;
        }
        $session = session()->driver();
        if (!$session->has($this->classname)) {
            return false;
        }
        $params = $session->get($this->classname);
        if (!is_null($columnIndex)) {
            return array_get($params, $columnIndex, false);
        }
        return $params;
    }

    public function getSelected($columnIndex)
    {
        $column = $this->getSessionValue($columnIndex);
        return !($column) ? null : array_get($column, 'search.value');
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
