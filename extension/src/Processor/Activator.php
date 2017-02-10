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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension\Processor;

use Illuminate\Support\Fluent;
use Antares\Contracts\Extension\Factory;
use Antares\Contracts\Extension\Command\Activator as Command;
use Antares\Contracts\Extension\Listener\Activator as Listener;

class Activator extends Processor implements Command
{

    /**
     * Activate an extension.
     *
     * @param  \Antares\Contracts\Extension\Listener\Activator  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function activate(Listener $listener, Fluent $extension)
    {
        if ($this->factory->started($extension->get('name'))) {
            return $listener->abortWhenRequirementMismatched();
        }
        return $this->execute($listener, 'activation', $extension, $this->getActivationClosure());
    }

    /**
     * Get activation closure.
     *
     * @return callable
     */
    protected function getActivationClosure()
    {
        return function (Factory $factory, $name) {
            $factory->activate($name);
        };
    }

}
