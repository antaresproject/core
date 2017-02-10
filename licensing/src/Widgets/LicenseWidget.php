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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Widgets;

use Antares\Widgets\Adapter\AbstractWidget;

class LicenseWidget extends AbstractWidget
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'License Info Widget';

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        $license = app('antares.license')->validate();
        return view('antares/licensing::admin.widgets._license', ['license' => $license]);
    }

}
