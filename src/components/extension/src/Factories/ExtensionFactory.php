<?php

declare(strict_types=1);

namespace Antares\Extension\Factories;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Model\Extension;
use Antares\Extension\Repositories\ConfigRepository;
use Composer\Package\CompletePackageInterface;
use Composer\Package\Loader\JsonLoader;
use Composer\Package\PackageInterface;

class ExtensionFactory {

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var JsonLoader
     */
    protected $jsonLoader;

    /**
     * ExtensionFactory constructor.
     * @param ConfigRepository $configRepository
     * @param JsonLoader $jsonLoader
     */
    public function __construct(ConfigRepository $configRepository, JsonLoader $jsonLoader) {
        $this->configRepository = $configRepository;
        $this->jsonLoader       = $jsonLoader;
    }

    /**
     * @param string $composerJsonPath
     * @return PackageInterface
     */
    public function getComposerPackage(string $composerJsonPath) : PackageInterface {
        return $this->jsonLoader->load($composerJsonPath);
    }

    /**
     * @param string $composerJsonPath
     * @return ExtensionContract
     * @throws ExtensionException
     */
    public function create(string $composerJsonPath) : ExtensionContract {
        $package        = $this->jsonLoader->load($composerJsonPath);
        $rootPath       = $this->configRepository->getRootPath();
        $path           = dirname($composerJsonPath);

        $extensionPath  = str_replace([$rootPath . '/src', '/'], ['', DIRECTORY_SEPARATOR], $path);
        $directories    = explode(DIRECTORY_SEPARATOR, $extensionPath);
        $namespaceParts = [];

        foreach($directories as $directory) {
            $namespaceParts[] = studly_case($directory);
        }

        $rootNamespace = '\\Antares' . implode('\\', $namespaceParts);
        $rootNamespace = str_replace('\\Components', '', $rootNamespace);

        if($package instanceof CompletePackageInterface) {
            return new Extension($package, $path, $rootNamespace);
        }

        throw new ExtensionException('The found package in the [' . $composerJsonPath . '] path has invalid interface.');
    }

}
