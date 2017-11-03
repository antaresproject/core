<?php



namespace Antares\Extension\Contracts;

use Antares\Extension\Contracts\Config\SettingsContract;
use Composer\Package\CompletePackageInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

interface ExtensionContract extends Arrayable, Jsonable {

    const STATUS_AVAILABLE = 0;

    const STATUS_INSTALLED = 1;

    const STATUS_ACTIVATED = 2;

    /**
     * Returns the Composer package.
     *
     * @return CompletePackageInterface
     */
    public function getPackage() : CompletePackageInterface;

    /**
     * Returns the vendor name (the first part of composer .
     *
     * @return string
     */
    public function getVendorName() : string;

    /**
     * Returns the package name.
     *
     * @return string
     */
    public function getPackageName() : string;

    /**
     * Returns the full name with version prefixed by : character. Format <vendor>-<package>:<version>
     *
     * @return string
     */
    public function getNameWithVersion() : string;

    /**
     * Returns the path relative to the system root.
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Returns the root namespace.
     *
     * @return string
     */
    public function getRootNamespace() : string;

    /**
     * Determines if the extension has been installed already.
     *
     * @return bool
     */
    public function isInstalled() : bool;

    /**
     * Sets the extension status.
     *
     * @param int $status
     */
    public function setStatus(int $status);

    /**
     * Returns the extension status.
     *
     * @return int
     */
    public function getStatus() : int;

    /**
     * Determines if the extension has been activated already.
     *
     * @return bool
     */
    public function isActivated() : bool;

    /**
     * Sets if the extension should be required for system.
     *
     * @param bool $state
     */
    public function setIsRequired(bool $state);

    /**
     * Determines if the extension is required for the whole system.
     *
     * @return bool
     */
    public function isRequired():  bool;

    /**
     * Sets the settings.
     *
     * @param SettingsContract $settings
     */
    public function setSettings(SettingsContract $settings);

    /**
     * Returns the settings.
     *
     * @return SettingsContract
     */
    public function getSettings() : SettingsContract;

    /**
     * Returns friendly short name of the module from the composer extra attribute.
     *
     * @return string
     */
    public function getFriendlyName() : string;

    /**
     * Returns the component type.
     *
     * @return string
     */
    public function getFriendlyType() : string;

}
