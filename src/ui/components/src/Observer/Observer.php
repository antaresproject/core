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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Observer;

use Illuminate\Support\Facades\Cache;

class Observer
{

    /**
     * remove cache on save
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function saving($model)
    {
        Cache::forget(config('antares/ui-components::cache'));
    }

}
