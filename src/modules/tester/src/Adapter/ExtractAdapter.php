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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Adapter;

use Antares\Model\Component;
use Antares\Tester\Contracts\Extractor;
use Illuminate\Support\Fluent;
use ReflectionClass;

class ExtractAdapter implements Extractor
{

    /**
     * generates inline scripts to handle button click event
     * 
     * @param array $params
     */
    public function generateScripts(array $params = null)
    {
        $cball = <<<CBALL
(function(window,$){ 
            $('body').on('click', '#%s', function () {
                $('div.test-results').remove();
                var form=$(this).parents('form:first');                
                $.ajax({
                    url:"%s",
                    data:form.serialize(),
                    type:"POST",
                    beforeSend:function(){                        
                        form.find('input,button,select,textarea').addClass('btn-disabled').attr('disabled','disabled');
                    },
                    success:function(response){
                        $('<div/>').attr('class','test-results').html(response).insertAfter(form);
                        form.find('input,button,select,textarea').removeClass('btn-disabled').removeAttr('disabled');
                    },
                    error:function(response){
                        form.find('input,button,select,textarea').removeClass('btn-disabled').removeAttr('disabled');
                    }
                });
                return false;
            });      
   })(window,jQuery);

CBALL;

        $inlineScript = sprintf($cball, $params['id'], handles('antares::tools/tester/run', ['csrf' => true]));
        app('antares.asset')
                ->container(app('config')->get('antares/tester::container'))
                ->inlineScript($params['id'], $inlineScript);
    }

    /**
     * extracting form properties
     * 
     * @return array
     */
    public function extractForm($className = null)
    {
        $traced   = $this->findTraced($className);
        $form     = $traced['args'][1]['form'];
        $controls = [];

        foreach ($form->fieldsets as $fieldset) {
            foreach ($fieldset->controls as $control) {
                $controls[$control->name] = $this->resolveFieldValue($control->name, $form->row, $control);
            }
        }
        $this->extractHiddens($controls, $form->hiddens);

        $name           = $this->extractModuleName($traced);
        $name = str_replace('_', '-', $name);
        $name = str_replace('components/', 'antaresproject/component-', $name);

        $memory = app('antares.memory')->get("extensions.active.{$name}");
        if (is_null($memory)) {
            $memory = ['fullname' => 'core'];
        }

        $component  = Component::findOneByName($memory['fullname']);

        $attributes = [
            'component_id' => $component->id,
            'component'    => $name,
            'controls'     => $controls
        ];
        return $attributes;
    }

    /**
     * find traced form
     * 
     * @param String $className
     * @return mixed
     */
    private function findTraced($className = null)
    {
        $traces     = debug_backtrace();
        $reflection = new ReflectionClass($className);
        $filename   = $reflection->getFileName();
        $traced     = null;


        foreach ($traces as $trace) {
            if (isset($trace['file']) and $filename == $trace['file']) {
                $traced = $trace;
                break;
            }
        }
        return $traced;
    }

    /**
     * extract hidden elements from form
     * 
     * @param array $controls
     * @param array $hiddens
     */
    private function extractHiddens(&$controls, array $hiddens = null)
    {
        if (!empty($hiddens)) {
            $dom   = new \DOMDocument('1.0');
            $nodes = [];
            foreach ($hiddens as $hidden) {
                $dom->loadHTML($hidden);
                $nodes = $dom->getElementsByTagName('input');
            }
            foreach ($nodes as $node) {
                $controls[$node->getAttribute('name')] = $node->getAttribute('value');
            }
        }
    }

    /**
     * extracting module path from debug_backtrace
     * 
     * @param array $traced
     * @return String
     */
    protected function extractModuleName(array $traced)
    {
        $executor = str_replace(realpath(app_path() . '/../src'), '', $traced['file']);

        $extractedPath = explode(DIRECTORY_SEPARATOR, $executor);
        $names         = [];
        foreach ($extractedPath as $index => $directory) {
            if ($directory == 'src') {
                break;
            }
            if ($directory !== '' && $directory !== 'modules') {
                $names[] = $directory;
            }
        }

        return implode('/', $names);
    }

    /**
     * resolve field value
     * 
     * @param String $name
     * @param String $row
     * @param Fluent $control
     * @return String | mixed
     */
    protected function resolveFieldValue($name, $row, Fluent $control)
    {
        $value = null;
        $model = data_get($row, $name);
        if (!is_null($model)) {
            $value = $model;
        }

        if (is_null($control->get('value'))) {
            return $value;
        }

        $value = $control->get('value');
        if ($value instanceof Closure) {
            $value = $value($row, $control);
        }

        return $value;
    }

}
