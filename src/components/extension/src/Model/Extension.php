<?php

declare(strict_types = 1);

namespace Antares\Extension\Model;

use Antares\Extension\Config\Settings;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\ExtensionContract;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use Illuminate\Support\Arr;

class Extension implements ExtensionContract
{

    /**
     * @var PackageInterface
     */
    protected $package;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var string
     */
    protected $rootNamespace = '';

    /**
     * @var int
     */
    protected $status = ExtensionContract::STATUS_AVAILABLE;

    /**
     * @var bool
     */
    protected $isRequired = false;

    /**
     * @var string
     */
    protected $vendorName;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var SettingsContract
     */
    protected $settings;

    /**
     * Extension constructor.
     * @param CompletePackageInterface $package
     * @param string $path
     * @param string $rootNamespace
     */
    public function __construct(CompletePackageInterface $package, string $path, string $rootNamespace)
    {
        $this->package       = $package;
        $this->path          = $path;
        $this->rootNamespace = $rootNamespace;
        $this->settings      = new Settings();

        list($this->vendorName, $this->packageName) = explode('/', $this->package->getName());
    }

    /**
     * Returns the Composer package.
     *
     * @return CompletePackageInterface
     */
    public function getPackage(): CompletePackageInterface
    {
        return $this->package;
    }

    /**
     * Returns the vendor name (the first part of composer .
     *
     * @return string
     */
    public function getVendorName(): string
    {
        return $this->vendorName;
    }

    /**
     * Returns the package name.
     *
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * Returns the full name with version prefixed by : character. Format <vendor>-<package>:<version>
     *
     * @return string
     */
    public function getNameWithVersion(): string
    {
        return $this->getPackage()->getName() . ':' . $this->getPackage()->getPrettyVersion();
    }

    /**
     * Returns the path relative to the system root.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the root namespace.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

    /**
     * Sets the extension status.
     *
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * Returns the extension status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Determines if the extension has been installed already.
     *
     * @return bool
     */
    public function isInstalled(): bool
    {
        return $this->status === ExtensionContract::STATUS_INSTALLED || $this->isActivated();
    }

    /**
     * Determines if the extension has been activated already.
     *
     * @return bool
     */
    public function isActivated(): bool
    {
        return $this->status === ExtensionContract::STATUS_ACTIVATED;
    }

    /**
     * Sets if the extension should be required for system.
     *
     * @param bool $state
     */
    public function setIsRequired(bool $state)
    {
        $this->isRequired = $state;
    }

    /**
     * Determines if the extension is required for the whole system.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * Sets the settings.
     *
     * @param SettingsContract $settings
     */
    public function setSettings(SettingsContract $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Returns the settings.
     *
     * @return SettingsContract
     */
    public function getSettings(): SettingsContract
    {
        return $this->settings;
    }

    /**
     * Returns friendly name of the module from the composer extra attribute.
     *
     * @return string
     */
    public function getFriendlyName(): string
    {
        $extra       = $this->getPackage()->getExtra();
        $regularName = str_replace(['component-', 'module-'], '', $this->getPackageName());

        return (string) Arr::get($extra, 'friendly-name', $regularName);
    }

    /**
     * Returns the component type.
     *
     * @return string
     */
    public function getFriendlyType(): string
    {
        return Types::getTypeByExtension($this);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'         => $this->package->getName(),
            'vendorName'   => $this->getVendorName(),
            'packageName'  => $this->getPackageName(),
            'version'      => $this->package->getVersion(),
            'required'     => $this->isRequired(),
            'installed'    => $this->isInstalled(),
            'activated'    => $this->isActivated(),
            'path'         => $this->getPath(),
            'namespace'    => $this->getRootNamespace(),
            'friendlyName' => $this->getFriendlyName(),
            'type'         => $this->getFriendlyType(),
            'status'       => $this->getStatus(),
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

}
