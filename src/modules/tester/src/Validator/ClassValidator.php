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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Validator;

use Antares\Tester\Contracts\ClassValidator as ValidatorContract;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

class ClassValidator implements ValidatorContract
{

    /**
     * validate container attributes
     * 
     * @param array $attributes
     * @return boolean
     */
    public function isValid(array $attributes = [])
    {
        try {
            if (!isset($attributes['validator'])) {
                return false;
            }
            $reflection = new ReflectionClass($attributes['validator']);

            if (!$reflection->hasMethod('__invoke')) {
                return false;
            }
            if (!in_array('Antares\Tester\Contracts\Tester', $reflection->getInterfaceNames())) {
                return false;
            }
        } catch (\Exception $e) {
            Log::emergency($e);
            return false;
        }
        return true;
    }

}
