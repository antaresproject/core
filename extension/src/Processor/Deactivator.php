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
use Antares\Contracts\Extension\Command\Deactivator as Command;
use Antares\Contracts\Extension\Listener\Deactivator as Listener;

class Deactivator extends Processor implements Command
{

    /**
     * Deactivate an extension.
     *
     * @param  \Antares\Contracts\Extension\Listener\Deactivator  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function deactivate(Listener $listener, Fluent $extension)
    {
        if (!$this->factory->started($extension->get('name')) && !$this->factory->activated($extension->get('name'))) {
            return $listener->abortWhenRequirementMismatched();
        }
        $this->factory->deactivate($extension->get('name'));

        return $listener->deactivationHasSucceed($extension);
    }

}
