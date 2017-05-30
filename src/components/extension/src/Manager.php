<?php

declare(strict_types = 1);

namespace Antares\Extension;

use Antares\Extension\Collections\Extensions;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Factories\SettingsFactory;
use Antares\Extension\Model\ExtensionModel;
use Antares\Extension\Repositories\ExtensionsRepository;
use Antares\Support\Collection;
use Dingo\Api\Facade\Route;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Manager
{

    /**
     * Filesystem finder instance.
     *
     * @var FilesystemFinder
     */
    protected $filesystemFinder;

    /**
     * Extensions repository instance.
     *
     * @var ExtensionsRepository
     */
    protected $extensionsRepository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var SettingsFactory
     */
    protected $settingsFactory;

    /**
     * Collection of available packages.
     *
     * @var Extensions|ExtensionContract[]|null
     */
    public $availableExtensions;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * Manager constructor.
     * @param FilesystemFinder $filesystemFinder
     * @param ExtensionsRepository $extensionsRepository
     * @param Filesystem $filesystem
     * @param SettingsFactory $settingsFactory
     */
    public function __construct(FilesystemFinder $filesystemFinder, ExtensionsRepository $extensionsRepository, Filesystem $filesystem, SettingsFactory $settingsFactory)
    {
        $this->filesystemFinder     = $filesystemFinder;
        $this->extensionsRepository = $extensionsRepository;
        $this->filesystem           = $filesystem;
        $this->settingsFactory      = $settingsFactory;
    }

    /**
     * Returns the collection of available extensions.
     *
     * @return Extensions|ExtensionContract[]
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function getAvailableExtensions(): Extensions
    {
        if ($this->availableExtensions instanceof Extensions) {
            return $this->availableExtensions;
        }

        $foundExtensions = $this->filesystemFinder->findExtensions();

        $storedExtensions = app('antares.installed') ? $this->extensionsRepository->all() : new Collection();

        foreach ($foundExtensions as $foundExtension) {
            $extension = $storedExtensions->first(function(ExtensionModel $extensionModel) use($foundExtension) {
                return $extensionModel->getFullName() === $foundExtension->getPackage()->getName();
            });

            $configFile = $foundExtension->getPath() . '/resources/config/settings.php';

            if ($this->filesystem->exists($configFile)) {
                $foundExtension->setSettings($this->settingsFactory->createFromConfig($configFile));
            }

            if ($extension instanceof ExtensionModel) {
                $foundExtension->setStatus($extension->getStatus());
                $foundExtension->setIsRequired($extension->isRequired());
                $foundExtension->getSettings()->updateData($extension->getOptions());
            }
        }

        return $this->availableExtensions = $foundExtensions;
    }

    /**
     * Verify whether the extension is installed by the given name.
     *
     * @param string $name
     * @return bool
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function isInstalled(string $name): bool
    {
        $extension = $this->getAvailableExtensions()->findByName($this->getNormalizedName($name));

        if ($extension instanceof ExtensionContract) {
            return $extension->isInstalled();
        }

        return false;
    }

    /**
     * Verify whether the extension is active by the given name.
     *
     * @param string $name
     * @return bool
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function isActive(string $name): bool
    {
        $extension = $this->getAvailableExtensions()->quessByName($name);
        if ($extension instanceof ExtensionContract) {
            return $extension->isActivated();
        }
        return false;
    }

    public function route($name, $default = '/')
    {
        $app = app();

        if (!isset($this->routes[$name])) {
            $key = "antares/extension::handles.{$name}";

            $this->routes[$name] = new RouteGenerator($app->make('config')->get($key, $default), $app->make('request'));
        }

        return $this->routes[$name];
    }

    /**
     * @param string $name
     * @return string
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function getExtensionPathByName(string $name): string
    {
        $extension = $this->getAvailableExtensions()->quessByName($name);

        if ($extension instanceof ExtensionContract) {
            return $extension->getPath();
        }

        return '';
    }

    /**
     * Gets active extension by path name
     * 
     * @param String $path
     * @return String
     */
    public function getActiveExtensionByPath(string $path)
    {
        $activated = $this->getAvailableExtensions()->filterByActivated();
        foreach ($activated as $extension) {
            if (starts_with($path, $extension->getPath())) {
                return $extension;
            }
        }
        return false;
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return ExtensionContract|null
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function getExtensionByVendorAndName(string $vendor, string $name)
    {
        return $this->getAvailableExtensions()->findByVendorAndName($vendor, $name);
    }

    /**
     * @param string $name
     * @return Contracts\Config\SettingsContract|null
     * @throws ExtensionException
     * @throws FileNotFoundException
     */
    public function getSettings(string $name)
    {
        $extension = $this->getAvailableExtensions()->findByName($this->getNormalizedName($name));

        if ($extension instanceof ExtensionContract) {
            return $extension->getSettings();
        }

        return null;
    }

    /**
     * Checks if the given extension has settings form.
     *
     * @param string $name
     * @return bool
     */
    public function hasSettingsForm(string $name): bool
    {
        $extension = $this->getAvailableExtensions()->findByName($this->getNormalizedName($name));

        if ($extension instanceof ExtensionContract) {
            if ($extension->getSettings()->getCustomUrl()) {
                return true;
            }

            return app()->make(SettingsFormResolver::class)->hasSettingsForm($extension);
        }

        return false;
    }

    /**
     * @param \Closure|null $callback
     */
    public function after(\Closure $callback = null)
    {
        app()->make(Dispatcher::class)->after($callback);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getNormalizedName(string $name): string
    {
        if (!Str::contains($name, '/')) {
            $name = 'antaresproject/component-' . $name;
        }

        return $name;
    }

    /**
     * get actual extension name based on route
     *
     * @return String
     * @deprecated
     */
    public function getActualExtension()
    {
        $route = Route::getCurrentRoute();

        if ($route instanceof \Illuminate\Routing\Route) {
            $action = $route->getActionName();

            if ($action === 'Closure') {
                return false;
            }

            preg_match("/.+?(?=\\\)(.*)\Http/", $action, $matches);
            return empty($matches) ? false : strtolower(trim($matches[1], '\\'));
        }

        return false;
    }

}
