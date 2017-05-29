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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Templates;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;

class Ajax extends AbstractTemplate
{

    /**
     * Name of template used by ui component
     * 
     * @var String
     */
    protected $template = 'ajax';

    /**
     * Constructing
     */
    public function __construct()
    {
        parent::__construct();
        $url = array_get($this->attributes, 'remote');
        if (!is_null($url)) {
            $handles = handles($url);
            $script  = $this->load($handles, $this->id);
            if (app('request')->ajax()) {
                echo '<script type="text/javascript">' . $script . '</script>';
            } else {
                $this->attributes['script'] = $script;
                app('antares.asset')->container('antares/foundation::scripts')->inlineScript('ajax_widget_' . str_random(3), $script);
            }
        }
    }

    /**
     * Renders widget content
     */
    public function render()
    {
        
    }

    /**
     * default process results
     * 
     * @return String
     */
    protected function load($url, $id)
    {
        return <<< JS
                $(document).ready(function(){    
                var widgetContainer=$('.grid-stack-item[id=$id]').find('.widget-content');
                var parentWidgetContainer=widgetContainer.parent();                    
                if($('.jquery-modal.current').length>0){
                    widgetContainer=$('.jquery-modal').find('.widget-content');
                    parentWidgetContainer=widgetContainer.closest('.card--enlarged');
                }
                parentWidgetContainer.LoadingOverlay('show');                
                $.ajax({
                   url:"$url",
                   type: "GET",
                   success:function(response){
                        widgetContainer.html(response);
                   },
                   error:function(response){                        
                        widgetContainer.html('<div class="alert alert--glow alert--error alert--lg alert--border mb20" "><i class="alert__icon zmdi zmdi-alert-circle"></i><span><strong>Unable to load widget content:</strong><br/>'+response.statusText+'</span></div>');
                   },
                   complete:function(){
                        parentWidgetContainer.LoadingOverlay('hide');                        
                   }
                });
                });
JS;
    }

}
