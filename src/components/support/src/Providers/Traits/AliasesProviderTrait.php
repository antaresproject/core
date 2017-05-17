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


namespace Antares\Support\Providers\Traits;

use Illuminate\Foundation\AliasLoader;

trait AliasesProviderTrait
{

    /**
     * Register facades aliases.
     *
     * @return void
     */
    protected function registerFacadesAliases()
    {
        $loader = AliasLoader::getInstance();

        foreach ((array) $this->facades as $facade => $aliases) {
            foreach ((array) $aliases as $alias) {
                $loader->alias($alias, $facade);
            }
        }
    }

    /**
     * Register the class aliases in the container.
     *
     * @return void
     */
    protected function registerCoreContainerAliases()
    {
        foreach ((array) $this->aliases as $key => $aliases) {
            foreach ((array) $aliases as $alias) {
                $this->app->alias($key, $alias);
            }
        }
    }

}
