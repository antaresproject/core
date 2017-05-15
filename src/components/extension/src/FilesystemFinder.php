<?php

declare(strict_types = 1);

namespace Antares\Extension;

use Antares\Extension\Collections\Extensions;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Factories\ExtensionFactory;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Repositories\ConfigRepository;
use Antares\Extension\Validators\ExtensionValidator;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class FilesystemFinder
{

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var ExtensionValidator
     */
    protected $extensionValidator;

    /**
     * @var ExtensionFactory
     */
    protected $extensionFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected static $validTypes = [
        'antaresproject-component',
        'antaresproject-module',
    ];

    /**
     * FilesystemFinder constructor.
     * @param ConfigRepository $configRepository
     * @param ExtensionValidator $extensionValidator
     * @param ExtensionFactory $extensionFactory
     * @param Filesystem $filesystem
     */
    public function __construct(ConfigRepository $configRepository, ExtensionValidator $extensionValidator, ExtensionFactory $extensionFactory, Filesystem $filesystem)
    {
        $this->configRepository   = $configRepository;
        $this->extensionValidator = $extensionValidator;
        $this->extensionFactory   = $extensionFactory;
        $this->filesystem         = $filesystem;
    }

    /**
     * Returns the collection with all available extensions in the file system.
     *
     * @return Extensions|ExtensionContract[]
     * @throws ExtensionException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function findExtensions(): Extensions
    {
        $extensions = new Extensions();


        foreach ($this->configRepository->getPaths() as $path) {
            $composerPattern = $this->configRepository->getRootPath() . '/' . $path . '/composer.json';
            $composerFiles   = $this->filesystem->glob($composerPattern);



            if ($composerFiles === false) {
                throw new ExtensionException('Error occurs when looking for extensions in the [' . $composerPattern . '] path.');
            }

            foreach ($composerFiles as $composerFile) {
                $package = $this->extensionFactory->getComposerPackage($composerFile);

                if (!in_array($package->getType(), self::$validTypes, true)) {
                    continue;
                }

                $extension = $this->extensionFactory->create($composerFile);

                if (!$this->extensionValidator->isValid($extension)) {
                    throw new ExtensionException('The extension [' . $extension->getPackage()->getName() . '] is not valid.');
                }

                $extensions->push($extension);
            }
        }

        return $extensions;
    }

    /**
     * Resolves a component or a module namespace by the given file path.
     *
     * @param string $path
     * @param bool $asPackage
     * @return string
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException
     */
    public function resolveNamespace(string $path, bool $asPackage = false): string
    {
        $file       = new File($path);
        $pathInfo   = array_filter(explode(DIRECTORY_SEPARATOR, trim(str_replace([$this->configRepository->getRootPath(), 'src'], '', $file->getRealPath()), DIRECTORY_SEPARATOR)));
        $prefix     = 'antares';
        $namespaces = [];

        foreach ($pathInfo as $name) {
            if ($name === 'core') {
                $namespaces[] = $asPackage ? 'foundation' : $name;
                break;
            }
            if ($name === 'app') {
                $namespaces[] = 'foundation';
                break;
            }
            if ($name === 'src') {
                break;
            }
            if (in_array($name, ['components', 'modules'], true)) {
                continue;
            }
            $namespaces[] = $name;
        }

        return $prefix . '/' . implode('/', $namespaces);
    }

    /**
     * Resolve extension path.
     *
     * @param  string  $path
     * @return string
     */
    public function resolveExtensionPath($path): string
    {
        $app  = rtrim(base_path('app'), '/');
        $base = rtrim(base_path(), '/');

        return str_replace(
                ['app::', 'vendor::antares', 'base::'], ["{$app}/", "{$base}/src", "{$base}/"], $path
        );
    }

}
