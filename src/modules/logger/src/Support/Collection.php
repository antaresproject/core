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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Support;

use Illuminate\Support\Collection as IlluminateCollection;

class Collection extends IlluminateCollection
{

    /**
     * Reset the collection.
     *
     * @return self
     */
    public function reset()
    {
        $this->items = [];

        return $this;
    }

}
