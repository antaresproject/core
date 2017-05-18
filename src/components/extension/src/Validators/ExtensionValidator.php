<?php

declare(strict_types=1);

namespace Antares\Extension\Validators;

use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Repositories\ConfigRepository;
use File;

class ExtensionValidator {

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * ExtensionValidator constructor.
     * @param ConfigRepository $configRepository
     */
    public function __construct(ConfigRepository $configRepository) {
        $this->configRepository = $configRepository;
    }

    /**
     * Determines if the extension is set properly.
     *
     * @param ExtensionContract $extension
     * @return bool
     */
    public function isValid(ExtensionContract $extension) : bool {
        return ! in_array($extension->getPackage()->getName(), $this->configRepository->getReservedNames(), true);
    }

    /**
     * Check whether the package has a writable public asset.
     *
     * @param ExtensionContract $extension
     * @throws ExtensionException
     */
    public function validateAssetsPath(ExtensionContract $extension) {
        $targetPath = $this->configRepository->getPublicPath() . '/packages/antares';

        if( ! File::isWritable($targetPath) ) {
            throw new ExtensionException('The package [' . $extension->getPackage()->getName() . '] cannot access to the public assets path in [' . $targetPath . '].');
        }
    }

}
