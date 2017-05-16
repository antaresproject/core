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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Logger\Widgets;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Antares\Model\User;

class UserDetailsWidget extends AbstractTemplate
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'User Details';

    /**
     * Template for widget
     *
     * @var String
     */
    protected $template = 'empty';

    /**
     * widget attributes
     *
     * @var array
     */
    protected $attributes = [
        'x'              => 0,
        'y'              => 0,
        'min_width'      => 2,
        'min_height'     => 15,
        'max_width'      => 12,
        'max_height'     => 15,
        'default_width'  => 15,
        'default_height' => 2,
    ];

    /**
     * Where widget should be available 
     *
     * @var array
     */
    protected $views = [
        'antares/foundation::admin.users.show'
    ];

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        $user = User::query()->findOrFail(from_route('user'));
        return view('antares/logger::admin.widgets.user_details', ['user' => $user])->render();
    }

}
