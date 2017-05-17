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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Contracts\Http\Middleware;

use Illuminate\Contracts\Routing\Registrar;

interface ModuleNamespaceResolver
{

    /**
     * object instance
     * 
     * @param Registrar $registrar
     */
    public function __construct(Registrar $registrar);

    /**
     * module namespace resolver
     * 
     * @param String $name
     * @param array $matches
     */
    public function resolve($matches = []);

    /**
     * cleared namespace of module
     * 
     * @return String
     */
    public function getClear();
}
