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
use Antares\Brands\Processor\Email;

interface BrandTemplateing
{

    /**
     * on email branding
     */
    public function update(Email $processor, $id = null);

    /**
     * when validation dont give a shit
     */
    public function updateFailedValidation($errors, $id);

    /**
     * when brand email update error
     */
    public function updateFailed(BrandModel $model, array $errors);

    /**
     * when brand email updated successfully
     */
    public function updated($model);
}
