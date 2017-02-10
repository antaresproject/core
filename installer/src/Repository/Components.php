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


namespace Antares\Installation\Repository;

use Illuminate\Contracts\Container\Container;
use Antares\Contracts\Extension\Factory;
use Antares\Memory\Model\DeferedEvent;
use Illuminate\Support\Facades\Event;

class Components
{

    /**
     * extension activator
     *
     * @var Factory
     */
    protected $factory;

    /**
     * required extensions
     *
     * @var array 
     */
    protected $required = [];

    /**
     * migrate manager instance
     *
     * @var \Antares\Publisher\MigrateManager 
     */
    protected $manager;

    /**
     * extension finder instance
     *
     * @var \Antares\Extension\Finder
     */
    protected $finder;

    /**
     * memory instance
     *
     * @var \Antares\Memory\Provider
     */
    protected $memory;

    /**
     * constructing
     * 
     * @param Container $container
     * @param Factory $factory
     */
    public function __construct(Container $container, Factory $factory)
    {
        $this->factory  = $factory;
        $this->required = config('installer.required', []);
        $this->manager  = $container->make('antares.publisher.migrate');
        $this->finder   = $container->make('antares.extension.finder');
        $this->memory   = $container->make('antares.memory')->make('primary');
    }

    /**
     * stores and runs components migration files
     * 
     * @param array $input
     * @return boolean
     */
    public function store(array $input = [])
    {
        $list       = $this->finder->detect();
        $extensions = array_get($input, 'extension', []);
        $install    = array_merge($this->required, $extensions);

        $this->factory->detect();
        $this->factory->finish();
        foreach ($install as $component) {
            $this->migrateExtension($component, $list);
        }
        Event::fire('antares.install.components');
        $this->memory->put('app.installed', 1);
        $this->memory->finish();
        return true;
    }

    /**
     * runs extension migration files
     * 
     * @param array $component
     * @param array $list
     * @return boolean
     */
    protected function migrateExtension($component, $list)
    {
        $data = $list->get($component);
        if (!isset($data['path'])) {
            return false;
        }
        $vendor = $this->finder->resolveExtensionVendorPath($data['path']);
        $this->manager->package($vendor);
        $this->manager->seed($component);
        $this->factory->up($component);
        $this->factory->refresh($component);
        DeferedEvent::query()->getModel()->newInstance(['name' => "after.install.{$component}"])->save();
        return;
    }

}
