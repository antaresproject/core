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

namespace Antares\Datatables\Adapter;

use Illuminate\Support\Collection;

class ColumnFilterAdapter
{

    /**
     * Columns container
     *
     * @var Collection
     */
    protected $columns;

    /**
     * Datatable classname
     *
     * @var String
     */
    protected $target;

    /**
     * Construct
     * 
     * @param Collection $columns
     * @param String $target
     */
    public function __construct(Collection $columns, $target = null)
    {
        $this->columns   = $columns;
        $this->classname = $target;
    }

    /**
     * Get session keyname
     * 
     * @return String
     */
    protected function key()
    {
        return strtolower(implode('-', [class_basename($this->classname), uri()]));
    }

    /**
     * Shows column filter
     * 
     * @return string
     */
    public function __toString()
    {
        if (is_null($this->columns) or $this->columns->isEmpty()) {
            return '';
        }
        return view('datatables-helpers::partials._columns_filter', ['columns' => $this->columns, 'key' => $this->key()])->render();
    }

    /**
     * Saves visibility columns definition
     * 
     * @param array $params
     * @return boolean
     */
    public function save(array $params = [])
    {
        $index      = array_get($params, 'index');
        $visibility = array_get($params, 'visible');
        $key        = array_get($params, 'key');

        if (is_null($index) or is_null($visibility) or is_null($key)) {
            return false;
        }
        $request = request();
        $session = $request->session();

        if ($session->has($key)) {
            $data         = $session->get($key);
            $data[$index] = $visibility === "true";
            $session->remove($key);
        } else {
            $data = [$index => $visibility === "true"];
        }
        $session->put($key, $data);
        $session->save();
        return $data;
    }

    /**
     * Gets column visibility definition
     * 
     * @return array
     */
    public function getColumns()
    {
        $session = request()->session();
        $key     = $this->key();
        if (!$session->has($key)) {
            return $this->columns;
        }
        $config = $session->get($key);
        foreach ($this->columns as $index => $column) {
            if (!isset($config[$index])) {
                continue;
            }
            $column->visible = $config[$index];
        }

        return $this->columns;
    }

}
