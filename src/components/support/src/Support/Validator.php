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


namespace Antares\Support;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Validation\Factory;
use Antares\Support\Traits\ValidationTrait;

abstract class Validator
{

    use ValidationTrait;

    /**
     * List of rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * List of events.
     *
     * @var array
     */
    protected $events = [];

    /**
     * List of phrases.
     *
     * @var array
     */
    protected $phrases = [];

    /**
     * Create a new Validator instance.
     *
     * @param  \Illuminate\Contracts\Validation\Factory  $factory
     * @param  \Illuminate\Contracts\Events\Dispatcher  $dispatcher
     */
    public function __construct(Factory $factory, Dispatcher $dispatcher)
    {
        $this->validationFactory    = $factory;
        $this->validationDispatcher = $dispatcher;
    }

    /**
     * Create a scope scenario.
     *
     * @param  string  $scenario
     * @param  array   $parameters
     *
     * @return Validator
     */
    public function on($scenario, array $parameters = [])
    {
        return $this->onValidationScenario($scenario, $parameters);
    }

    /**
     * Add bindings.
     *
     * @param  array  $bindings
     *
     * @return Validator
     */
    public function bind(array $bindings)
    {
        return $this->bindToValidation($bindings);
    }

    /**
     * Execute validation service.
     *
     * @param  array  $input
     * @param  string|array  $events
     * @param  array   $phrases
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function with(array $input, $events = [], array $phrases = [])
    {
        return $this->runValidation($input, $events, $phrases);
    }

    /**
     * Get validation events.
     *
     * @return array
     */
    public function getValidationEvents()
    {
        return $this->events;
    }

    /**
     * Get validation phrases.
     *
     * @return array
     */
    public function getValidationPhrases()
    {
        return $this->phrases;
    }

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->rules;
    }

}
