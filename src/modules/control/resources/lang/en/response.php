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



return
        array(
            'db-failed'         => 'Unable to finish process. Database error.',
            'no-access-to-edit' => 'Your permissions do not allow you to perform this action.',
            'acls'              =>
            array(
                'update' => 'ACL has been updated',
            ),
            'themes'            =>
            array(
                'extracted-success' => 'Theme has been extracted. Now, you can activate extracted theme.',
                'extracted-error'   => 'Theme has not been extracted. Error appears while extraction package data.',
            ),
            'users'             =>
            array(
                'created' => 'User has been created',
                'updated' => 'User has been updated',
                'deleted' => 'User has been deleted',
            ),
            'roles'             =>
            array(
                'created' => 'User group has been created successfully',
                'updated' => 'User group has been updated successfully',
                'deleted' => 'User group has been deleted successfully',
            ),
            'db-failed'         => 'Error while saving changes'
);
