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


namespace Antares\Brands\Listener;

use Antares\Brands\Model\Brands as BrandModel;

interface BrandUpdater extends Brand
{

    /**
     * Response when update brand page succeed.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showBrandChanger(array $data);

    /**
     * Response when update brand failed on validation.
     *
     * @param  \Illuminate\Support\MessageBag|array  $errors
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function updateBrandFailedValidation($errors, $id);

    /**
     * Response when updating brand failed.
     *
     * @param BrandModel $brand
     * @param  array  $errors
     * @return mixed
     */
    public function updateBrandFailed(BrandModel $brand, array $errors);

    /**
     * Response when updating brand succeed.
     *
     * @param BrandModel $brand
     * @return mixed
     */
    public function brandUpdated(BrandModel $brand);
}
