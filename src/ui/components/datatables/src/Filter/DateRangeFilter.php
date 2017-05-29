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


namespace Antares\Datatables\Filter;

class DateRangeFilter extends AbstractFilter
{

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = '%value';

    /**
     * Filter attributes
     *
     * @var array
     */
    protected $attributes = [
        'row_title' => 'antares/automation::messages.executed_at'
    ];

    /**
     * Values getter
     * 
     * @return array
     */
    protected function getValues()
    {
        $uri    = uri();
        $params = $this->session->get($uri . '.' . get_called_class());
        if (is_null($params) or ! isset($params['column']) or ! $params['column'] == $this->column) {
            return [];
        }
        if (empty($params['value'])) {
            $params = $this->session->get($uri);
            unset($params[get_called_class()]);
            $this->session->put($uri, $params);
            $this->session->save();
        } else {
            return $params['value'];
        }

        return [];
    }

    /**
     * Renders sidebar
     * 
     * @param array $data
     * @return String
     */
    public function sidebar(array $data = array())
    {
        $uri        = uri();
        $params     = app('request')->session()->get($uri);
        $classname  = get_called_class();
        $attributes = array_get($params, $classname);
        if (!empty($attributes) and $attributes['column'] == $this->column) {
            $value   = json_decode($attributes['value'], true);
            $start   = array_get($value, 'start');
            $end     = array_get($value, 'end');
            $name    = trans(array_get($this->attributes, 'row_title'), ['start' => $start, 'end' => $end]);
            $subview = $this->render($start, $end);

            return view('datatables-helpers::partials._deleted_date_range')->with([
                        'column'    => $this->column,
                        'instance'  => $this,
                        'route'     => uri(),
                        'name'      => $name,
                        'classname' => $classname,
                        'subview'   => $subview
                    ])->render();
        }

        return parent::sidebar();
    }

    /**
     * renders filter
     * 
     * @return String
     */
    public function render($start = null, $end = null)
    {
        publish('automation', ['js/automation_date_range_filter.js']);
        return view('antares/automation::admin.partials._date_range_filter', ['start' => $start, 'end' => $end])->render();
    }

}
