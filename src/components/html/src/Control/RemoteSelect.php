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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Html\Control;

use Antares\Asset\JavaScriptDecorator;
use Antares\Asset\JavaScriptExpression;

class RemoteSelect extends Control
{

    /**
     * @var string the name of the jQuery plugin
     */
    public $pluginName = 'select2';

    /**
     * handler variable name
     *
     * @var Stirng 
     */
    protected $handler;

    /**
     * renders infinity select control
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $params     = isset($this->params['attributes']) ? $this->params['attributes'] : $this->params;
        $id         = !isset($params['id']) ? $params['name'] . '-' . str_random(4) : $params['id'];
        $script     = $this->script($id, $params);
        $attributes = app('html')->decorate(array_except($params + ['id' => $id], ['pluginOptions', 'options']));


        $optionsData = array_get($this->params, 'optionsData', []);
        $control     = app('form')->select(
                array_get($this->params, 'name'), array_get($this->params, 'options', []), array_get($this->params, 'value'), $attributes, $optionsData
        );
        echo view('antares/html::controls.remote_select', ['control' => $control])->render();
        if (app('request')->ajax()) {
            echo '<script type="text/javascript">' . $script . '</script>';
        } else {
            app('antares.asset')->container('antares/foundation::scripts')->inlineScript('remote-select-' . $id, $script);
        }
        return '';
    }

    /**
     * creates javascript select scripts
     * 
     * @param type $id
     * @return type
     */
    protected function script($id, $params)
    {
        $handler       = $this->handler = 'handler' . str_random(4);
        $options       = array_replace_recursive($this->defaults()['pluginOptions'], $params['pluginOptions']);
        $decorated     = JavaScriptDecorator::decorate($options);
        return $this->init($id) . $handler . '.' . $this->pluginName . '(' . $decorated . ')';
    }

    /**
     * default plugin properties
     * 
     * @return array
     */
    protected function defaults()
    {
        return [
            'options'       => ['placeholder' => 'Search for a repo ...'],
            'pluginOptions' => [
                'allowClear'              => true,
                'minimumInputLength'      => 0,
                'minimumResultsForSearch' => 'Infinity',
                'ajax'                    => [
                    'dataType'       => 'json',
                    'delay'          => 250,
                    'data'           => new JavaScriptExpression($this->ajaxData()),
                    'processResults' => new JavaScriptExpression($this->processResults()),
                    'cache'          => true
                ],
                'escapeMarkup'            => new JavaScriptExpression($this->escapeMarkup()),
                'templateResult'          => new JavaScriptExpression($this->templateResult()),
                'templateSelection'       => new JavaScriptExpression($this->templateSelection()),
            ],
        ];
    }

    /**
     * create select handler
     * 
     * @param String $id
     * @return String
     */
    protected function init($id)
    {
        $handler = $this->handler;
        return <<< JS
            var $handler=$('#$id');
JS;
    }

    /**
     * default process results
     * 
     * @return String
     */
    protected function processResults()
    {
        return <<< JS
            function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 20) < data.total
                    }
                };
            }
JS;
    }

    /**
     * fieldname getter
     * 
     * @return String
     */
    protected function getFieldname()
    {
        return array_get($this->params['attributes'], 'fieldname', $this->params['name']);
    }

    /**
     * default ajax data
     * 
     * @return String
     */
    protected function ajaxData()
    {

        $name = $this->getFieldname();
        return <<< JS
            function (params) {
                return {
                    q: params.term,
                    field: '$name',
                    page: params.page,
                    per_page: '20'
                };
            }
JS;
    }

    /**
     * default template selection
     * 
     * @return String
     */
    protected function templateSelection()
    {
        $name = $this->getFieldname();
        return <<< JS
            function (repo) {
                if (repo.selected) {
                    return repo.text;
                }
                return repo.$name;
            }
JS;
    }

    /**
     * default template result
     * 
     * @return String
     */
    protected function templateResult()
    {
        $name = $this->getFieldname();
        return <<< JS
            function (repo) {
                if (repo.selected) {
                    return repo.text;
                }
                return repo.$name;
            }
JS;
    }

    /**
     * default escape markup
     * 
     * @return String
     */
    protected function escapeMarkup()
    {
        return <<< JS
            function (m) {
                return m;
            }
JS;
    }

}
