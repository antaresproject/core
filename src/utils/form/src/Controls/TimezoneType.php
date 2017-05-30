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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

use Antares\Brands\Model\Country;
use Antares\Form\Controls\Elements\Option;

class TimezoneType extends SelectType
{

    protected $emptyValue = 'Select Timezone';

    /**
     * CountryType constructor.
     *
     * @param string $name
     * @param array  $attributes
     */
    public function __construct($name, $attributes = [])
    {
        parent::__construct($name, $attributes);
        $this->setTimezones();
    }

    /**
     * Fill select with countries saved in DB
     */
    private function setTimezones()
    {
        foreach(\DateTimeZone::listIdentifiers() as $identifier) {
            $key = strtolower(str_replace('/', '_', $identifier));
            $this->valueOptions[] = new Option($key, $identifier);
        }
    }

}
