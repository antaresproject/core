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
 namespace Antares\Http;

use Antares\Support\Traits\ValidationTrait;
use Illuminate\Foundation\Http\FormRequest as Request;

class FormRequest extends Request
{
    use ValidationTrait;

    /**
     * Get validation rules.
     *
     * @return array
     */
    public function getValidationRules()
    {
        return $this->container->call([$this, 'rules']);
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Validation\Validator
     */
    protected function getValidatorInstance()
    {
        $this->setupValidationScenario();
        $this->setupValidationParameters();

        $this->validationFactory    = $this->container->make('Illuminate\Contracts\Validation\Factory');
        $this->validationDispatcher = $this->container->make('Illuminate\Contracts\Events\Dispatcher');

        return $this->runValidation($this->all(), [], $this->messages());
    }

    /**
     * Setup validation scenario based on request method.
     *
     * @return void
     */
    protected function setupValidationScenario()
    {
        $current   = $this->method();
        $available = [
            'POST'   => 'store',
            'PUT'    => 'update',
            'DELETE' => 'destroy',
        ];

        if (in_array($current, $available)) {
            $this->onValidationScenario($available[$current]);
        }
    }

    /**
     * Setup validation scenario based on request method.
     *
     * @return void
     */
    protected function setupValidationParameters()
    {
        $parameters = $this->route()->parametersWithoutNulls();

        $this->bindToValidation($parameters);
    }
}
