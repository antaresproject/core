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

class SelectFilter extends AbstractFilter
{

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = '%value';

    /**
     * Placeholder for select filter
     *
     * @var String 
     */
    protected $placeholder = null;

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
     * {@inheritdoc}
     */
    public function sidebar(array $data = array())
    {
        $subview = $this->render();
        $values  = $this->getValues();
        if (empty($values)) {
            return '';
        }
        $name      = str_replace('%value', implode(', ', array_only($this->options(), $values)), trans($this->pattern));
        $classname = get_called_class();
        return view('antares/automation::admin.partials._deleted')->with([
                    'column'    => $this->column,
                    'instance'  => $this,
                    'route'     => uri(),
                    'name'      => $name,
                    'classname' => $classname,
                    'subview'   => $subview
                ])->render();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        publish('automation', ['js/automation_status_filter.js']);
        $selected    = $this->getValues();
        $placeholder = is_null($this->placeholder) ? trans('antares/foundation::messages.select_placeholder_default', ['name' => strtolower($this->name)]) : $this->placeholder;
        return view('datatables-helpers::partials._filter_select_multiple', [
                    'options'     => $this->options(),
                    'column'      => $this->column,
                    'placeholder' => $placeholder,
                    'selected'    => $selected
                ])->render();
    }

}
