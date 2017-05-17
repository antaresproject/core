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

        $supportedRules = [];
        foreach ($controls as $control) {
            $ruleName = $this->getMatchedRuleNameForControl($control);
            if ($ruleName) {
                $supportedRules[$ruleName] = $this->rules[$ruleName];
            }
        }

        return $supportedRules;
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

    /**
     * Gets the matched rule name by the provided control name.
     *
     * @param string $control
     * @return string | null
     */
    public function getMatchedRuleNameForControl($control)
    {
        foreach ($this->rules as $name => $rule) {
            if (self::isRuleMatchedToControl($name, $control)) {
                return $name;
            }
        }
    }

    /**
     * @param string $rule Rule name
     * @param string $control Control name
     * @return bool
     */
    public static function isRuleMatchedToControl($rule, $control)
    {
        return str_is($rule, self::getNormalizedControlName($control));
    }

    /**
     * Gets a normalized control name.
     * 
     * @param string $control
     * @return string mixed
     */
    protected static function getNormalizedControlName($control)
    {
        $replaceMap = [
            ']' => '',
            '[' => '',
        ];

        foreach ($replaceMap as $search => $replace) {
            $control = str_replace($search, $replace, $control);
        }

        return $control;
    }

}
