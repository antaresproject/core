<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension\Processor;

use Symfony\Component\ClassLoader\ClassMapGenerator;

class NameSpacer
{

    /**
     * Path to vendors
     * 
     * @var String
     */
    private $vendorDir;

    /**
     * Path to directory of module
     * 
     * @var String
     */
    private $directory = false;

    /**
     * Type Of operation
     * 
     * @var String
     */
    private $operation = false;

    /**
     * Singleton Instance
     * 
     * @var NameSpacer
     */
    private static $oInstance = false;

    /**
     * singleton instance
     * 
     * @param String $category
     * @param String $package
     * @param String $operation
     * 
     * @return Self
     */
    public static function getInstance($category, $package, $operation = null)
    {
        if (self::$oInstance == false) {
            self::$oInstance = new NameSpacer($category, $package, $operation);
        }
        return self::$oInstance;
    }

    /**
     * Setup a new Instance.
     *
     * @param String $category
     * @param String $package
     * @param String $operation
     */
    public function __construct($category, $package, $operation = null)
    {

        $this->vendorDir = realpath(app_path() . '/../src');
        $brokenPath      = explode('/', $package);
        $package         = (strpos($package, '/') !== FALSE) ? end($brokenPath) : $package;
        $this->directory = realpath($this->vendorDir . '/modules/' . implode(DIRECTORY_SEPARATOR, [$category, $package]));

        $this->operation = $operation;
    }

    /**
     * standarize directories path separator
     * 
     * @param array $classmap
     * 
     * @return array
     */
    private function standardDirectories(array &$classmap)
    {
        foreach ($classmap as &$path) {
            $path = str_replace('\\', '/', str_replace($this->vendorDir, '', $path));
        }

        return $classmap;
    }

    /**
     * get module class namespaces
     * 
     * @return array
     */
    private function getModuleNameSpace()
    {
        if ($this->directory == false) {
            return;
        }
        $classmap = ClassMapGenerator::createMap($this->directory);

        return $this->standardDirectories($classmap);
    }

    /**
     * get autoload_namespace content
     * 
     * @return array
     */
    private function getApplicationNameSpace()
    {

        $classmap = require($this->vendorDir . '/../vendor/composer/autoload_classmap.php');
        return $this->standardDirectories($classmap);
    }

    /**
     * autoload namespace decorator
     * 
     * @param array $classmap
     * @return String
     */
    private function decorate(array $classmap)
    {

        $content = sprintf("<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 \n\t\$vendorDir = dirname(dirname(__FILE__));\n\t\$baseDir = dirname(\$vendorDir);\n\n return %s;", var_export($classmap, true));

        $replace = [
            "'" . $this->vendorDir,
            str_replace('\\', '\\\\', "'" . $this->vendorDir)
        ];
        return str_replace($replace, "\$vendorDir . '", $content);
    }

    /**
     * rewrite module namespaces
     */
    public function rewrite()
    {
//        $this->command('dumpautoload', )
//        exit;
//        $moduleClassMap = $this->getModuleNameSpace();
//        if (is_null($moduleClassMap)) {
//            return;
//        }
//        $applicationClassMap = $this->getApplicationNameSpace();
//        $classmap            = null;
//        if (in_array($this->operation, ['uninstall', 'delete'])) {
//            $classmap = array_diff($applicationClassMap, $moduleClassMap);
//        }
//        if (in_array($this->operation, ['install', 'activate'])) {
//            $classmap = array_merge($applicationClassMap, $moduleClassMap);
//        }
//        if (!is_null($classmap)) {
//            $decoration = $this->decorate($classmap);
//            file_put_contents($this->vendorDir . '/../vendor/composer/autoload_classmap.php', $decoration);
//        }
    }

}
