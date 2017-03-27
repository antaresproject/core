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
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

use Antares\Brands\Model\Country;
use Antares\Form\Controls\Elements\Option;

class CountryType extends SelectType
{

    protected $emptyValue = 'Select Country';

    /**
     * CountryType constructor.
     *
     * @param string $name
     * @param array  $attributes
     */
    public function __construct($name, $attributes = [])
    {
        parent::__construct($name, $attributes);
        $this->setCountriesFromDB();
    }

    /**
     * Fill select with countries saved in DB
     */
    private function setCountriesFromDB()
    {
        /** @var Country $item */
        foreach(app(Country::class)->all() as $item) {
            $this->valueOptions[] = new Option($item->id, $item->name, ['data-country' => $item->code]);
        }
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $this->setAttribute('data-flag-select--search', 'true');
        $this->addWrapper(['class' => 'input-field input-field--icon']);
        $this->prependHtml = sprintf('<span class="input-field__icon"><span class="flag-icon flag-icon-us"></span></span>');

        return parent::render();
    }

}
