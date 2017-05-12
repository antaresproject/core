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

namespace Antares\Brands\Composers;

class BrandPlaceHolder
{

    /**
     * up component placeholders
     */
    public function onBootExtension()
    {
        $brand = app('antares.memory')
                ->make('primary')
                ->get('brand');
        return app('antares.widget')->make('placeholder.brands')->add('brands')->value(view('antares/foundation::brands.brand', ['brand' => $brand]));
    }

}
