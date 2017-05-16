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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Command;

use Illuminate\Contracts\Foundation\Application;
use Antares\Contracts\Authorization\Authorization;
use Antares\Control\Contracts\Command\Synchronizer as SynchronizerContract;

class Synchronizer implements SynchronizerContract
{

    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The authorization implementation.
     *
     * @var \Antares\Contracts\Authorization\Authorization
     */
    protected $acl;

    /**
     * Construct a new class.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Antares\Contracts\Authorization\Authorization  $acl
     */
    public function __construct(Application $app, Authorization $acl)
    {
        $this->app = $app;
        $this->acl = $acl;
    }

    /**
     * Re-sync administrator access control.
     *
     * @return void
     */
    public function handle()
    {
        $admin = $this->app->make('antares.role')->admin();
        $this->acl->allow($admin->name, ['Manage Users', 'Manage Antares', 'Manage Roles', 'Manage Acl']);
    }

}
