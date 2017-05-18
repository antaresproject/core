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

use Antares\UI\UIComponents\Contracts\AfterValidate;

class AfterValidateAdapter implements AfterValidate
{

    /**
     * create after validate inline script
     * 
     * @param array | mixed $params
     * @return String
     */
    public function afterValidate($params = null)
    {

        $script = <<<EOD
js:function(form, data, hasError) { 
    if(hasError===false){
        
        container = form.parents('.panel:first').find('.panel-body:first');
        
        $.ajax({
            url: "%s",                
            success: function (response) {
                container.html(response);        
            }
        });
    }
       }
EOD;
        return sprintf($script, $params);
    }

}
