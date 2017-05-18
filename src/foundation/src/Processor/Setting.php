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

namespace Antares\Foundation\Processor;

use Antares\Contracts\Foundation\Listener\SettingUpdater as SettingUpdateListener;
use Antares\Contracts\Foundation\Listener\SystemUpdater as SystemUpdateListener;
use Antares\Contracts\Foundation\Command\SettingUpdater as SettingUpdateCommand;
use Antares\Contracts\Foundation\Command\SystemUpdater as SystemUpdateCommand;
use Antares\Foundation\Http\Presenters\Setting as Presenter;
use Antares\Foundation\Validation\Setting as Validator;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Config;
use Antares\Memory\MemoryManager;
use Illuminate\Support\Fluent;

class Setting extends Processor implements SystemUpdateCommand, SettingUpdateCommand
{

    /**
     * The memory provider implementation.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    protected $memory;

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Foundation\Http\Presenters\Setting  $presenter
     * @param  \Antares\Foundation\Validation\Setting  $validator
     * @param  MemoryManager  $memory
     */
    public function __construct(Presenter $presenter, Validator $validator, MemoryManager $memory)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
        $this->memory    = $memory->make('primary');
    }

    /**
     * View setting page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\SettingUpdater  $listener
     *
     * @return mixed
     */
    public function edit(SettingUpdateListener $listener)
    {
        $memory = $this->memory;

        $eloquent = new Fluent([
            'mode' => $memory->get('site.mode', 'development')
        ]);

        $form = $this->presenter->form($eloquent);
        return $listener->showSettingChanger(compact('eloquent', 'form'));
    }

    /**
     * Update setting.
     *
     * @param  \Antares\Contracts\Foundation\Listener\SettingUpdater  $listener
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(SettingUpdateListener $listener, array $input)
    {
        $input = new Fluent($input);
        $form  = $this->presenter->form($input);
        if (!$form->isValid()) {
            return $listener->settingFailedValidation($form->getMessageBag());
        }
        $memory = $this->memory;
        $memory->put('site.mode', $input['mode']);
        $memory->finish();
        return $listener->settingHasUpdated();
    }

    /**
     * Migrate Antares components.
     *
     * @param  \Antares\Contracts\Foundation\Listener\SystemUpdater  $listener
     *
     * @return mixed
     */
    public function migrate(SystemUpdateListener $listener)
    {
        Foundation::make('antares.publisher.asset')->foundation();
        Foundation::make('antares.publisher.migrate')->foundation();

        return $listener->systemHasUpdated();
    }

    /**
     * Resolve value or grab from configuration.
     *
     * @param  mixed   $input
     * @param  string  $alternative
     *
     * @return mixed
     */
    private function getValue($input, $alternative)
    {
        if (empty($input)) {
            $input = Config::get($alternative);
        }

        return $input;
    }

}
