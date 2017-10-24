<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Http\Resolver;

use Antares\Contracts\Http\Middleware\ModuleNamespaceResolver as ModuleResolverContract;
use Antares\Extension\Factories\ExtensionFactory;
use Illuminate\Contracts\Routing\Registrar;
use Composer\Package\CompletePackageInterface;
use RuntimeException;
use ReflectionClass;

class ModuleNamespaceResolver implements ModuleResolverContract
{

    /**
     * frontend default component content name
     */
    const frontendContentComponent = 'content';

    /**
     * map core names
     * 
     * @var String
     */
    private static $core = ['foundation', 'users'];

    /**
     * key registered for core
     *
     * @var String 
     */
    private static $registeredForCore = 'acl_antares';

    /**
     * name of namespace prefix
     * 
     * @var String
     */
    private static $namespacePrefix = 'antares';

    /**
     * @var \Illuminate\Routing\Route 
     */
    protected $route;

    /**
     * name of module 
     * 
     * @var String
     */
    protected $name;

    /**
     * @var CompletePackageInterface|null
     */
    protected $package;

    /**
     * Extension factory instance.
     *
     * @var ExtensionFactory
     */
    protected $extensionFactory;

    /**
     * ModuleNamespaceResolver constructor.
     * @param Registrar $registrar
     */
    public function __construct(Registrar $registrar)
    {
        $this->extensionFactory = app()->make(ExtensionFactory::class);

        $current = $registrar->current();

        if ($current !== null) {
            $this->route = $current->getAction();
        }
    }

    /**
     * module namespace resolver
     * 
     * @param array $matches
     * @return String
     * @throws RuntimeException
     */
    public function resolve($matches = [])
    {
        if (!isset($this->route['controller'])) {
            return false;
        }

        list($controller,) = explode('@', $this->route['controller']);

        $fileName = (new ReflectionClass($controller))->getFileName();
        $rootPath = substr($fileName, 0, strrpos($fileName, 'src'));
        $composerFilePath = $rootPath . 'composer.json';

        if($rootPath === '' || ! file_exists($composerFilePath)) {
            return false;
        }

        $this->package = $this->extensionFactory->getComposerPackage($composerFilePath);
        $this->name = $this->package->getName();

        return $this->name;
    }

    /**
     * cleared namespace of module
     * 
     * @return String
     */
    public function getClear()
    {
        $return = str_replace(self::$namespacePrefix . '/', '', $this->name);
        $exists = array_where(self::$core, function($key) use($return) {
            return $return == $key;
        });
        if (!empty($exists)) {
            return self::$registeredForCore;
        }
        if ($return == 'app') {
            return 'content';
        }

        $name = ($return == 'app') ? self::frontendContentComponent : $return;

        return str_replace('antaresproject/', '', $name);
    }

    /*
     * resolve action name from route 
     * 
     * @return String
     */

    public function getAction($matches = [])
    {
        if (!isset($this->route['controller'])) {
            return false;
        }
        $controller = $this->route['controller'];
        preg_match("/@(.*)/", $controller, $matches);
        if (!isset($matches[1])) {
            throw new RuntimeException('Unable to resolve action name from controller.');
        }
        return $matches[1];
    }

    /**
     * resolver controller name from route
     * 
     * @param array $matches
     * @return boolean|String
     */
    public function getController($matches = [])
    {

        if (!isset($this->route['controller'])) {
            return false;
        }
        $controller = $this->route['controller'];
        preg_match("/.+?(?=Controllers)(.*)@/", $controller, $matches);
        if (!isset($matches[1])) {
            return false;
        }
        return str_ireplace(['Controllers', 'Controller', '\\'], "", $matches[1]);
    }

}
