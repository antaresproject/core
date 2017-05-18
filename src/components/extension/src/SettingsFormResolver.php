<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Extension\Contracts\Config\SettingsFormContract;
use Antares\Extension\Contracts\ExtensionContract;
use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use DomainException;

class SettingsFormResolver {

    /**
     * Container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * Postfix for the Form class.
     *
     * @var string
     */
    protected static $formClassName = '\\Config\\SettingsForm';

    /**
     * SettingsImporter constructor.
     * @param Container $container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Checks if the given extension has settings form.
     *
     * @param ExtensionContract $extension
     * @return bool
     */
    public function hasSettingsForm(ExtensionContract $extension) : bool {
        $className = $extension->getRootNamespace() . self::$formClassName;

        return class_exists($className);
    }

    /**
     * Returns the instance of settings form by the given extension.
     *
     * @param ExtensionContract $extension
     * @return SettingsFormContract
     * @throws InvalidArgumentException
     * @throws DomainException
     */
    public function tryGetSettingsForm(ExtensionContract $extension) : SettingsFormContract {
        $className = $extension->getRootNamespace() . self::$formClassName;

        if( ! class_exists($className) ) {
            throw new InvalidArgumentException('The provided class name [' . $className . '] does not exist.');
        }

        $instance = $this->container->make($className);

        if($instance instanceof SettingsFormContract) {
            return $instance;
        }

        throw new DomainException('The instance of the found class does not implement a valid interface.');
    }

}
