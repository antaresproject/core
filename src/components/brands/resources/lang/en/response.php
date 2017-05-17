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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


return array(
    'upload'    => [
        'error' => 'Unable to upload image.',
    ],
    'create'    => array(
        'success'   => 'Brand has been added successfully',
        'db-failed' => 'An error occurs while creating brand.'
    ),
    'update'    => array(
        'success'   => 'Brand has been updated.',
        'db-failed' => 'An error occurs while updating brand.'
    ),
    'delete'    => array(
        'default-deletion' => 'Cannot delete default brand',
        'success'          => 'Brand has been deleted',
        'db-failed'        => 'The requested brand does not exist.'
    ),
    'change'    => array(
        'success' => 'Active brand has been changed.',
    ),
    'notexists' => 'The selected brand does not exist.'
);
