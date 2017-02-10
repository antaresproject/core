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


namespace Antares\Extension\Console;

use Illuminate\Support\Fluent;
use Illuminate\Console\ConfirmableTrait;
use Antares\Extension\Processor\Activator as Processor;
use Antares\Contracts\Extension\Listener\Activator as Listener;
use Antares\Contracts\Extension\Factory;

class ActivateCommand extends ExtensionCommand implements Listener
{

    use ConfirmableTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:activate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Activate an extension.';
    protected $factory;
    protected $extension   = null;

    public function __construct()
    {
        parent::__construct();
        $this->factory = app(Factory::class);
    }

    /**
     * Execute the console command.
     *
     * @param  \Antares\Extension\Processor\Activator  $activator
     *
     * @return void
     */
    public function handle(Processor $activator)
    {
        if (!$this->confirmToProceed()) {
            return;
        }
        $name = $this->argument('name');
        if ($this->validate($name)) {
            $fluent    = new Fluent(['name' => $this->extension['name'], 'uid' => $this->extension['name']]);
            $activator = app(Processor::class);
            //$this->extension
            return $activator->activate($this, $fluent);
        }
        return false;
    }

    protected function composerInstall()
    {
        
    }

    /**
     * validates extension
     * 
     * @return boolean
     */
    protected function validate($name)
    {
        $component  = app('antares.memory')->make('component');
        $extensions = $component->get('extensions.available');
        foreach ($extensions as $keyname => $data) {
            if (str_contains($keyname, $name)) {
                $this->extension = ['name' => $keyname, 'data' => $data];
                break;
            }
        }
        if (is_null($this->extension)) {
            $this->error("Unable to find extension [{$name}].");
            return false;
        }
        $active = array_keys($component->get('extensions.active'));
//        if (in_array($this->extension['name'], $active)) {
//            $this->error("Extension [{$name}] is active.");
//            return false;
//        }
        return true;
    }

    /**
     * Response when extension activation has failed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $errors
     *
     * @return mixed
     */
    public function activationHasFailed(Fluent $extension, array $errors)
    {
        $this->error("Unable to activate extension [{$extension->get('name')}].");
    }

    /**
     * Response when extension activation has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function activationHasSucceed(Fluent $extension)
    {
        $this->info("Extension [{$extension->get('name')}] activated.");
    }

}
