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


namespace Antares\Foundation\Processor\Extension;

use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Event;
use Antares\Support\Facades\Extension;
use Antares\Support\Facades\Foundation;
use Antares\Foundation\Processor\Processor;
use Antares\Foundation\Validation\Extension as Validator;
use Antares\Contracts\Extension\Command\Configure as Command;
use Antares\Foundation\Http\Presenters\Extension as Presenter;
use Antares\Contracts\Extension\Listener\Configure as Listener;

class Configure extends Processor implements Command
{

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Foundation\Http\Presenters\Extension  $presenter
     * @param  \Antares\Foundation\Validation\Extension  $validator
     */
    public function __construct(Presenter $presenter, Validator $validator)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
    }

    /**
     * View edit extension configuration page.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configure(Listener $listener, Fluent $extension)
    {
        if (!Extension::started($extension->get('name'))) {
            return $listener->abortWhenRequirementMismatched();
        }

        $memory = Foundation::memory();

                $activeConfig = (array) $memory->get("extensions.active.{$extension->get('name')}.config", []);
        $baseConfig   = (array) $memory->get("extension_{$extension->get('name')}", []);


        $eloquent = new Fluent(array_merge($activeConfig, $baseConfig));

                        $form = $this->presenter->configure($eloquent, $extension->get('name'));

        Event::fire("antares.form: extension.{$extension->get('name')}", [$eloquent, $form]);

        return $listener->showConfigurationChanger(compact('eloquent', 'form', 'extension'));
    }

    /**
     * Update an extension configuration.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, Fluent $extension, array $input)
    {

        if (!Extension::started($extension->get('name'))) {
            return $listener->suspend(404);
        }

        $validation = $this->validator->with($input, ["antares.validate: extension.{$extension->get('name')}"]);

        if ($validation->fails()) {
            return $listener->updateConfigurationFailedValidation($validation->getMessageBag(), $extension->uid);
        }

        $memory = Foundation::memory();
        $config = (array) $memory->get("extension.active.{$extension->get('name')}.config", []);
        $input  = new Fluent(array_merge($config, $input));

        unset($input['_token']);

        Event::fire("antares.saving: extension.{$extension->get('name')}", [& $input]);

        $memory->put("extensions.active.{$extension->get('name')}.config", $input->getAttributes());
        $memory->put("extension_{$extension->get('name')}", $input->getAttributes());

        Event::fire("antares.saved: extension.{$extension->get('name')}", [$input]);

        return $listener->configurationUpdated($extension);
    }

}
