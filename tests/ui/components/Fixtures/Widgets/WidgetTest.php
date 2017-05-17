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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */






namespace Antares\Widgets\Tests\Fixtures\Widgets;

use Antares\Widgets\Adapter\AbstractWidget;

class WidgetTest extends AbstractWidget
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'Widget';

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        return '';
    }

    public function form()
    {
        
    }

}
