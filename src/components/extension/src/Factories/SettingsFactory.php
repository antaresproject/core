<?php

declare(strict_types=1);

namespace Antares\Extension\Factories;

use Antares\Extension\Config\Settings;
use Antares\Extension\Contracts\Config\SettingsContract;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class SettingsFactory {

    /**
     * Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * SettingsFactory constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    /**
     * Returns the settings by the given configuration file.
     *
     * @param string $path
     * @return SettingsContract
     * @throws FileNotFoundException
     */
    public function createFromConfig(string $path) : SettingsContract {
        $configData = (array) $this->filesystem->getRequire($path);

        return $this->createFromData($configData);
    }

    /**
     * Returns the settings by the given configuration array.
     *
     * @param array $configData
     * @return SettingsContract
     * @throws FileNotFoundException
     */
    public function createFromData(array $configData) : SettingsContract {
        $data       = (array) Arr::get($configData, 'data', []);
        $rules      = (array) Arr::get($configData, 'rules', []);
        $phrases    = (array) Arr::get($configData, 'phrases', []);
        $customUrl  = (string) Arr::get($configData, 'custom_url', '');

        return new Settings($data, $rules, $phrases, $customUrl);
    }

}
