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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
return

        array(
            'solution'         => 'Solution',
            'modules'          =>
            array(
                'invalid-category'  => 'Module has invalid category. You can use only: domains, products, fraud, addons as module namespace.',
                'invalid-structure' => 'Module has invalid structure. Only PSR-4 is allowed.',
                'invalid-manifest'  => 'Module has invalid manifest file',
                'invalid-name'      => 'Module has invalid name. The name is reserved',
            ),
            'steps'            =>
            array(
                'account'     => 'Create Administrator',
                'application' => 'Application Information',
                'done'        => 'Done',
                'requirement' => 'Check Requirements',
                'install'     => 'Install Antares',
                'components'  => 'Select components'
            ),
            'auth'             =>
            array(
                'title'       => 'Authentication Setting',
                'driver'      => 'Driver',
                'requirement' =>
                array(
                    'driver' => 'Antares require Auth using the Eloquent Driver',
                ),
                'model'       => 'Model',
            ),
            'database'         =>
            array(
                'title'    => 'Database Setting',
                'type'     => 'Database Type',
                'host'     => 'Host',
                'name'     => 'Database Name',
                'username' => 'Username',
                'password' => 'Password',
            ),
            'hide-password'    => 'Database password is hidden for security.',
            'connection'       =>
            array(
                'status'  => 'Connection Status',
                'success' => 'Successful',
                'fail'    => 'Failed',
            ),
            'mysqlDumpCommand' =>
            array(
                'status'  => 'Mysqldump command availability',
                'success' => 'Available',
                'fail'    => 'Not available',
            ),
            'system'           =>
            array(
                'writablePublicPackages'   =>
                array(
                    'name'     => 'Packages directory',
                    'solution' => 'Change permissions to public\packages directory.',
                ),
                'writablePublic'           =>
                array(
                    'name'     => 'Public directory',
                    'solution' => 'Change permissions to public directory.',
                ),
                'writableTemp'             =>
                array(
                    'name'     => 'Temp directory',
                    'solution' => 'Change permissions to storage\temp directory.',
                ),
                'writableLogs'             =>
                array(
                    'name'     => 'Logs directory',
                    'solution' => 'Change permissions to 755 to :path directory.',
                ),
                'writableTickets'          =>
                array(
                    'name'     => 'Tickets directory',
                    'solution' => 'Change permissions to storage\tickets directory.',
                ),
                'writableBootstrapCache'   =>
                array(
                    'name'     => 'Boostrap cache directory',
                    'solution' => 'Change permissions to bootstrap\cache directory.',
                ),
                'writableComposerVendor'   =>
                array(
                    'name'     => 'Composer vendor directory',
                    'solution' => 'Change permissions to vendor directory.',
                ),
                'writableComposerJsonFile' =>
                array(
                    'name'     => 'composer.json file',
                    'solution' => 'Change permissions to composer.json file.',
                ),
                'writableComposerLockFile' =>
                array(
                    'name'     => 'composer.lock file',
                    'solution' => 'Change permissions to composer.lock file.',
                ),
                'apacheModules'            =>
                array(
                    'name'     => 'Apache Modules',
                    'solution' => 'Install missing apache modules.',
                ),
                'phpExtensions'            =>
                array(
                    'name'     => 'PHP Extensions',
                    'solution' => 'Install missing php extensions.',
                ),
                'version'                  =>
                array(
                    'name'     => 'PHP Version',
                    'solution' => 'Update your php version.',
                ),
                'title'                    => 'System Requirements',
                'description'              => 'Please ensure the following requirement is profilled before installing Antares.',
                'requirement'              => 'Requirement',
                'status'                   => 'Status',
                'writableStorage'          =>
                array(
                    'name'     => 'Storage directory',
                    'solution' => 'Change permissions to 755 to :path directory.',
                ),
                'writableAsset'            =>
                array(
                    'name'     => 'Assets directory',
                    'solution' => 'Change permissions to :path directory.',
                ),
            ),
            'status'           =>
            array(
                'work'  => 'Workable',
                'not'   => 'Not Workable',
                'still' => 'Still Workable',
            ),
            'user'             =>
            array(
                'duplicate' => 'Unable to install when there already user registered.',
            ),
            'verify'           => 'Verify result',
            'stopAndBack'      => 'Stop and back'
);
