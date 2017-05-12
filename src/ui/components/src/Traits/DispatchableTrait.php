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

namespace Antares\UI\UIComponents\Traits;

use Illuminate\Support\Collection;

trait DispatchableTrait
{

    /**
     * Booted indicator.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * Boot active extensions.
     *
     * @return $this
     */
    public function booted()
    {
        return $this->booted;
    }

    /**
     * Shutdown all extensions.
     *
     * @return $this
     */
    public function finish()
    {
        foreach ($this->components as $name => $options) {
            $this->dispatcher->finish($name, $options);
        }

        $this->components = new Collection();

        return $this;
    }

}
