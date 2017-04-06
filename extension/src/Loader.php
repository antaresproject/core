<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Foundation\Application;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class Loader {

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * List of services.
     *
     * @var array
     */
    protected $services = [];

    /**
     * Loader constructor.
     * @param Application $application
     * @param Manager $manager
     * @param Filesystem $filesystem
     */
    public function __construct(Application $application, Manager $manager, Filesystem $filesystem) {
        $this->application  = $application;
        $this->manager      = $manager;
        $this->filesystem   = $filesystem;
    }

    /**
     * @param ExtensionContract $extension
     * @throws FileNotFoundException
     */
    public function register(ExtensionContract $extension) {
        $filePath = $extension->getPath() . '/providers.php';

        if($this->filesystem->exists($filePath)) {
            $providers = (array) $this->filesystem->getRequire($filePath);

            foreach($providers as $provider) {
                $this->registerProvider($provider);
            }
        }
    }

    /**
     * @param string $providerClassName
     */
    protected function registerProvider(string $providerClassName) {
        $instance = $this->application->resolveProviderClass($providerClassName);

        if ($instance->isDeferred()) {
            $services = $this->application->getDeferredServices();

            foreach ($instance->provides() as $service) {
                $services[$service] = $providerClassName;
            }

            $this->application->setDeferredServices($services);
        }
        else {
            $this->application->register($instance);
        }

        $this->services[] = $providerClassName;
    }

}
