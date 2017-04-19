<?php

namespace Antares\Datatables\Adapter;

use Closure;

class OrderAdapter
{

    /**
     * Session key name preifx
     *
     * @var String 
     */
    protected static $prefix = 'order';

    /**
     *
     * @var type 
     */
    protected $classname;

    public function apply($builder)
    {
        $this->saveRequestedSessionKey();
    }

    public function setClassname($name)
    {
        $this->classname = strtolower(str_replace('\\', '_', $name));
        return $this;
    }

    public function setEngineInstance(&$engine)
    {
        $values = $this->getSessionValue();

        if (!$values) {
            return $this;
        }
        if (is_null($direction = array_get($values, 'dir')) or is_null($column    = array_get($values, 'column'))) {
            return $this;
        }

        $name = $engine->getColumnName($column, true);

        if (is_null($name)) {
            $name = $engine->getColumnNameByIndex($column);
        }

        if (str_contains($name, '*')) {
            $name = str_replace('*', 'id', $name);
        }
        $queryBuilder = $engine->getQuery();
        $fired        = event('datatables.order.' . $name, [&$queryBuilder, $direction], true);



        if (empty($fired)) {
            $queryBuilder->orderBy($name, $direction);
        }


        return $this;
    }

    public function saveRequestedSessionKey()
    {

        if (!($columns = ajax('order'))) {
            return false;
        }
        $value = collect($columns)->first(function($item, $key) use(&$index) {
            $value = array_get($item, 'column');
            return (!is_null($value) && $value !== "");
        });
        return $this->setSessionKeyValue($value);
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

    protected function getSessionKey()
    {
        return self::$prefix . '_' . $this->classname;
    }

    public function getSessionValue()
    {
        if (php_sapi_name() === 'cli') {
            return false;
        }

        $session = session()->driver();
        $key     = $this->getSessionKey();
        return !$session->has($key) ? false : $session->get($key);
    }

    public function getSelected()
    {
        return $this->getSessionValue();
    }

}
