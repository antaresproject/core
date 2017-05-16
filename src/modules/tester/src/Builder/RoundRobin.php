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



namespace Antares\Tester\Builder;

use Antares\Tester\Contracts\Builder as BuilderContract;

class RoundRobin extends Builder implements BuilderContract
{

    /**
     * generate scripts used as inline scripts
     * 
     * @return String
     */
    public function generateScripts(array $attributes = null)
    {
        $init = <<<EOD
           $(document).ready(function(){
                container=$('div.%s');                                     
                form=$('#%s');
                cancelButton=form.find('button.cancel-request');
                submitButton=form.find('button:submit');
                var xhrPool = [];
                $(document).ajaxSend(function (e, jqXHR, options) {
                    xhrPool.push(jqXHR);
                });
                $(document).ajaxComplete(function (e, jqXHR, options) {
                    xhrPool = $.grep(xhrPool, function (x) {
                        return x != jqXHR
                    });
                });
                var abort = function () {
                    $.each(xhrPool, function (idx, jqXHR) {
                        jqXHR.abort();
                    });
                    cancelButton.attr('disabled','disabled').addClass('btn-disabled');
                    submitButton.removeAttr('disabled').removeClass('btn-disabled');
                };
                var processStop = function () {
                    abort();                    
                }
                $.fn.analyzeSpecAjax = function (key) {
                    $.when(
                        $.ajax({                            
                            url: "%s",
                            type: "POST",
                            data: indexes[key],
                            beforeSend: function (element) {
                                $('div.test-response').append('<hr/>'+indexes[key].message);               
                            }
                        })
                    ).then(function (data, textStatus, jqXHR) {                        
                        $('div.test-response').append(data);
                        if(indexes[key+1]!==undefined){
                            $.fn.analyzeSpecAjax(key+1);                            
                        }else{
                            processStop();                            
                        }
                    });
                };
                var indexes={};
                form.on('click', 'button:submit', function (e) {                                        
                    handler=$(this).parents('form:first');
                    
                    $.ajax({
                       url:handler.attr('action'),
                       data:handler.serialize(),
                       type:'POST',
                       dataType:'json',
                       success:function(response,element,status){
                           
                           $('div.test-response').html(''); 
                           indexes=response;       
                           cancelButton.removeAttr('disabled').removeClass('btn-disabled');
                           submitButton.attr('disabled','disabled').addClass('btn-disabled');
                           $.fn.analyzeSpecAjax(0);                           
                       },
                       error:function(eResponse){
                           obj = jQuery.parseJSON(eResponse.responseText);
                           $('div.test-response').html('');                                               
                           for(var i=0;i<obj.length;i++){                                                              
                               $('div.test-response').append(obj[i].error);               
                           }                                                      
                       }
                    });                    
                    return false;
                });
                form.on('click', '.cancel-request', function (e) {                                        
                    processStop();
                    return false;
                });
                form.find('.test-checkboxes').on('ifChecked',function(e){
                    form.find('input:checkbox').iCheck('check');
                    processStop();
                    return true;                    
                });
                
                 form.find('.test-checkboxes').on('ifUnchecked',function(e){
                    form.find('input:checkbox').iCheck('uncheck'); 
                    processStop();
                    return true;                    
                });
                
            });
EOD;
        return sprintf($init, $attributes['response-container'], $attributes['form-container'], handles('antares::tools/tester/run', ['csrf' => true]));
    }

    /**
     * generates script
     * 
     * @param String $name
     * @param array $attributes
     * @param \Antares\Tester\Builder\Closure $callback
     */
    public function build($name = null, array $attributes = [], $callback = null)
    {
        if (!isset($attributes['response-container'])) {
            array_set($attributes, 'response-container', 'test-response');
        }
        if (!isset($attributes['form-container'])) {
            array_set($attributes, 'form-container', 'tester-form');
        }
        $script    = is_null($name) ? 'round' : $name;
        $container = app('antares.asset')->container('antares/foundation::scripts');
        $container->inlineScript($script, $this->generateScripts($attributes));
        if ($callback instanceof Closure) {
            call_user_func($callback, $container);
        }
    }

}
