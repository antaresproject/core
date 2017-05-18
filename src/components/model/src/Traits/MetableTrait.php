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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
 namespace Antares\Model\Traits;

use Antares\Model\Value\Meta;
use Illuminate\Contracts\Support\Arrayable;

trait MetableTrait
{
    /**
     * `meta` field accessor.
     *
     * @param  mixed  $value
     *
     * @return \Antares\Model\Value\Meta
     */
    public function getMetaAttribute($value)
    {
        $meta = [];

        if (! is_null($value)) {
            $meta = json_decode($value, true);
        }

        return new Meta($meta);
    }
    /**
     * `meta` field mutator.
     *
     * @param  mixed  $value
     *
     * @return void
     */
    public function setMetaAttribute($value = null)
    {
        $this->attributes['meta'] = $this->mutateMetaAttribute($value);
    }

    /**
     * Get value from mixed content.
     *
     * @param  mixed  $value
     *
     * @return mixed
     */
    protected function mutateMetaAttribute($value)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        } elseif (! is_array($value)) {
            $value = (array) $value;
        }

        return json_encode($value);
    }
}
