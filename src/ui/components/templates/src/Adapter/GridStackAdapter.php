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

namespace Antares\UI\UIComponents\Adapter;

use Antares\UI\UIComponents\Contracts\GridStack;
use Illuminate\Contracts\Config\Repository;
use Antares\Asset\Factory as AssetFactory;

class GridStackAdapter implements GridStack
{

    /**
     * @var AssetFactory 
     */
    protected $assetFactory;

    /**
     * @var Repository 
     */
    protected $config;

    /**
     * constructor
     * 
     * @param Repository $config
     * @param AssetFactory $assetFactory
     */
    public function __construct(Repository $config, AssetFactory $assetFactory)
    {
        $this->assetFactory = $assetFactory;
        $this->config       = $config;
    }

    /**
     * append scripts & style in scripts container in layout
     */
    public function scripts()
    {
        $assets = app('antares.asset');
        $assets->container('antares/foundation::application')
                ->add('webpack_forms_basic', '/webpack/forms_basic.js', ['app_cache'])
                ->add('webpack_gridstack', '/webpack/view_gridstack.js', ['app_cache']);
        $assets->container('antares/foundation::scripts')
                ->add('ui_components', '/packages/core/js/ui_components.js', ['webpack_gridstack'])->inlineScript('grid-stack', $this->inline());
    }

    /**
     * generate gridstack inline scripts
     * 
     * @return String
     */
    protected function inline()
    {
        $inline = <<<EOD
           $(document).ready(function(){                         
            var element =null;
            $('.grid-stack').on('resizestop', function(event, ui) {                               
                element = event.target;                
            });
            $('.grid-stack').on('dragstop', function(event, ui) {               
                element = event.target;                                
            });
            container=$('div.grid-stack:first'); 
            $('.grid-stack:first').on('change', function (e, items) { 
                if(!$('#widgets-edit').length){
                    return false;
                }
                if(items===undefined){
                    return false;
                }
                var widgets = {};                        
                for (i = 0; i < items.length; i++) {
                    item = $.extend({}, items[i]);                        
                    widgets[i]={
                        widgetId: $(items[i].el).find('input[name=current]').val(),
                        x: items[i].x,
                        y: items[i].y,
                        width: items[i].width,
                        height: items[i].height,                            
                    }
                }   
                current=(element!==undefined || element!==null)?$(element):null;
                id=(current!==null)?current.attr('id'):null;                
                ajax=$.ajax({
                   url: "%s",
                   type: "POST",
                   data:{ 
                       widgets:widgets,
                       from:'%s',
                       current:id
                   },                    
                   success:function(response,event, xhr){                        
                        
                        var ct = xhr.getResponseHeader("content-type") || "";                        
                        if(response.length<=0){
                            return;
                        }
                        if(ct==='application/json'){
                            return false;
                        }
                        if(current!==null){
                            current.find('.grid-stack-item-content .widget-content').html(response);
                        }
                   },
                   complete:function(){                        
                        element=null;
                   } 
                });
            });
        });
EOD;
        return sprintf($inline, handles('antares/ui-components::ui-components/grid', ['csrf' => true]), uri());
    }

}
