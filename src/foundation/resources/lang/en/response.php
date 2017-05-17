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
            'extensions' => [
                'activate'  => 'Extension has been activated',
                'migrate'   => 'Extension migrations has been down',
                'uninstall' => [
                    'success' => 'Extension has been uninstalled.'
                ],
                'configuration-success' => 'The extensions has been configured successfully.',
                'configuration-failed' => 'An error occurred during updating the extension configuration.',
            ],
            'safe-mode'  => 'Antares is running on safe mode.',
            'account'    =>
            array(
                'password' =>
                array(
                    'update'  => 'Your password has been updated',
                    'invalid' => 'Current password does not match our record, please try again',
                ),
                'profile'  =>
                array(
                    'update' => 'Your profile has been updated',
                ),
            ),
            'users'      =>
            array(
                'gravatar_has_been_set' => 'Gravatar picture has been set',
                'create'                => 'User has been created',
                'update'                => 'User has been updated',
                'delete'                => 'User has been deleted|Users has been deleted',
            ),
            'credential' =>
            array(
                'register'            =>
                array(
                    'email-fail' => 'Unable to send User Registration Confirmation e-mail',
                    'email-send' => 'User Registration Confirmation e-mail has been sent to your inbox',
                ),
                'invalid-combination' => 'Invalid user and password combination',
                'logged-in'           => 'You have been logged in',
                'logged-out'          => 'You have been logged out',
                'user-not-active'     => 'Account is not active.'
            ),
            'modules'    =>
            array(
                'extracted-success' => 'Module package has been successfully extracted into modules path. Now you can install module.',
                'extracted-error'   => 'Error appears while extracting module package. Please try again or verify installable structure.',
                'validator'         =>
                array(
                    'invalid-structure' => 'Module package has invalid structure. Package should contains valid manifest.json file and PSR-4 directories structure. For more details please take a look at docs section.',
                ),
            ),
            'settings'   =>
            array(
                'update'        => 'Application settings has been updated',
                'system-update' => 'Antares Foundation has been updated',
            ),
            'acl'        =>
            array(
                'not-allowed' => 'You are not allowed to perform this action',
            ),
            'db-failed'  => 'Unable to save. Please try again.'
);
