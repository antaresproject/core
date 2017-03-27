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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Traits;

use Antares\Form\Contracts\Attributable;

/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 27.03.17
 * Time: 14:05
 */
trait SelectTypeFunctionsTrait
{

    /**
     * Turn on or off search feature for this select
     *
     * @param bool $search
     * @return $this|bool
     */
    public function setSearch(bool $search)
    {
        if(!$this instanceof Attributable) {
            return $this;
        }
        $searchOption = 'data-selectar--search';
        if ($search) {
            $this->setAttribute($searchOption, true);
        } else {
            $this->removeAttribute($searchOption);
        }

        return $this;
    }

}