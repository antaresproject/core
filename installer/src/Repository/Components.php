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

use Antares\Extension\Processors\Installer;
use Illuminate\Contracts\Container\Container;
use Antares\Extension\Manager as ExtensionsManager;
use Antares\Memory\Model\DeferedEvent;
use Illuminate\Support\Facades\Event;

/**
 * Class Components
 * @package Antares\Installation\Repository
 *
 * @deprecated
 *
 * TODO: to remove
 */
class Components
{

    /**
     * extension activator
     *
     * @var ExtensionsManager
     */
    protected $extensionsManager;

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
     * @var Installer
     */
    protected $installer;

    /**
     * memory instance
     *
     * @var \Antares\Memory\Provider
     */
    protected $memory;

    /**
     * Components constructor.
     * @param Container $container
     * @param ExtensionsManager $extensionsManager
     * @param Installer $installer
     */
    public function __construct(Container $container, ExtensionsManager $extensionsManager, Installer $installer)
    {
        $this->extensionsManager    = $extensionsManager;
        $this->installer            = $installer;
        $this->required             = config('installer.required', []);
        $this->manager              = $container->make('antares.publisher.migrate');
        $this->memory               = $container->make('antares.memory')->make('primary');
    }

    /**
     * stores and runs components migration files
     *
     * @param array $input
     * @return boolean
     */
    public function store(array $input = [])
    {
        $extensions = array_get($input, 'extension', []);
        $install    = array_merge($this->required, $extensions);

        foreach ($install as $component) {
            $this->installExtension($component);
        }

        Event::fire('antares.install.components');

        $this->memory->put('app.installed', 1);
        $this->memory->finish();

        return true;
    }

    /**
     * Installs the given extension.
     *
     * @param string $component
     * @return boolean
     */
    protected function installExtension(string $component)
    {
        $extension = $this->extensionsManager->getAvailableExtensions()->findByName($component);

        $this->installer->run();

        DeferedEvent::query()->getModel()->newInstance(['name' => "after.install.{$component}"])->save();


//        $data = $list->get($component);
//        if (!isset($data['path'])) {
//            return false;
//        }
//        $vendor = $this->finder->resolveExtensionVendorPath($data['path']);
//        $this->manager->package($vendor);
//        $this->manager->seed($component);
//        $this->factory->up($component);
//        $this->factory->refresh($component);
//        DeferedEvent::query()->getModel()->newInstance(['name' => "after.install.{$component}"])->save();
//        return;
    }

}
