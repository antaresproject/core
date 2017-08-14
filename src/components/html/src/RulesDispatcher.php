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

namespace Antares\Html;

class RulesDispatcher
{

    /**
     * @var array
     */
    protected $rules;

    /**
     * RulesDispatcher constructor.
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Returns an array of rules which are supported by available controls.
     *
     * @param array $controls
     * @return array
     */
    public function getSupported(array $controls)
    {
        if (!$this->hasRules()) {
            return [];
        }
        return $this->rules;
    }

    /**
     * Checks if the provider rules are not empty.
     *
     * @return bool
     */
    public function hasRules()
    {
        return count($this->rules) > 0;
    }

}
