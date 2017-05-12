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


namespace Antares\Users\Bootstrap;

use Antares\Model\Role;
use Antares\Model\User;
use Illuminate\Contracts\Foundation\Application;

class UserAccessPolicy
{

    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Users\Application  $app
     *
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $this->matchCurrentUserToRoles($app);

        $this->attachAccessPolicyEvents($app);
    }

    /**
     * Match current user to roles.
     *
     * @param  \Illuminate\Contracts\Users\Application  $app
     *
     * @return void
     */
    protected function matchCurrentUserToRoles(Application $app)
    {
        $app->make('events')->listen('antares.auth: roles', function (User $user = null) {
            if (is_null($user)) {
                return;
            }
            return $user->getRoles();
        });
    }

    /**
     * Attach access policy events.
     *
     * @param  \Illuminate\Contracts\Users\Application  $app
     *
     * @return void
     */
    protected function attachAccessPolicyEvents(Application $app)
    {
        Role::observe($app->make('Antares\Model\Observer\Role'));
        Role::setDefaultRoles($app->make('config')->get('antares/foundation::roles'));
    }

}
