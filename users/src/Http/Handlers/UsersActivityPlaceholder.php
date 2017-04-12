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

namespace Antares\Users\Http\Handler;

use Antares\Users\Processor\Activity\UsersActivity;

class UsersActivityPlaceholder
{

    /** @var UsersActivity */
    protected $processor;

    /**
     * UsersActivityPlaceholder constructor.
     * @param UsersActivity $processor
     */
    public function __construct(UsersActivity $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Users Activity Placeholder
     *
     * @return void
     */
    public function handle()
    {
        if (!user()) {
            return;
        }
        app('antares.widget')
                ->make('placeholder.left-menu')
                ->add('user_activity_config')
                ->value(view('antares/foundation::users.partials._last_activity_config', ['period' => config('antares/users::check_activity_every')]));
    }

}
