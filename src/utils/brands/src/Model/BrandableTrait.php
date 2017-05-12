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


namespace Antares\Brands\Model;

trait BrandableTrait
{

    /**
     * overwrite @see \Illuminate\Database\Eloquent\Model::save
     * @param array $options
     */
    public function save(array $options = array())
    {
        if (is_null($this->brand_id)) {
            $brandId        = brand_id();
            $this->brand_id = $brandId;
        }
        return parent::save($options);
    }

}
