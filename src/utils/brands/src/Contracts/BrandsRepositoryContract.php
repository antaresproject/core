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


namespace Antares\Brands\Contracts;

interface BrandsRepositoryContract
{

    /**
     * finds default brand
     */
    public function findDefault();

    /**
     * sets default brand by brand id
     */
    public function setDefaultBrandById($id);

    /**
     * finds row by identifier
     */
    public function findById($id);
}
