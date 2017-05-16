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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Strategy\Sandbox;

use Antares\Updater\Contracts\Terminator as TerminatorContract;
use Illuminate\Filesystem\Filesystem as FilesContract;
use Illuminate\Contracts\Foundation\Application;
use Exception;

class Terminator extends AbstractStrategy implements TerminatorContract
{

    /**
     * files instance
     *
     * @var FilesContract 
     */
    protected $files;

    /**
     * eloquent instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * constructing
     * 
     * @param FilesContract $files
     * @param Application $app
     */
    public function __construct(FilesContract $files, Application $app)
    {
        $this->files = $files;
        $this->model = $app->make('Antares\Updater\Model\Sandbox');
    }

    /**
     * terminate sandbox creation process
     */
    public function terminate()
    {
        $this->clearStorage();
        $this->setEnvironment();
        $this->renameComposer();
        $this->copyStubs();
    }

    /**
     * clearing storage
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Terminator
     */
    protected function clearStorage()
    {
        $buildPath   = $this->getBuildPath();
        $directories = config('antares/updater::sandbox.files.clear');
        foreach ($directories as $directory) {
            $realPath = $buildPath . DIRECTORY_SEPARATOR . $directory;
            if ($this->files->isDirectory($realPath)) {
                $this->files->cleanDirectory($realPath);
            }
        }
        return $this;
    }

    /**
     * setting environment
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Terminator
     * @throws Exception
     */
    protected function setEnvironment()
    {
        $envSourcePath = base_path() . DIRECTORY_SEPARATOR . '.env';
        $envTargetPath = $this->getBuildPath() . DIRECTORY_SEPARATOR . '.env';
        if (!$this->files->exists($envSourcePath)) {
            throw new Exception('Environment file not exists.');
        }
        $this->files->copy($envSourcePath, $envTargetPath);
        $content = $this->files->get($envTargetPath);
        $this->files->put($envTargetPath, $this->parseContent($content));

        return $this;
    }

    /**
     * rename all composer autoload classname
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Terminator
     */
    protected function renameComposer()
    {
        $relativePath = 'vendor/composer/autoload_real.php';
        $className    = $this->getClassnameByFilePath($relativePath);
        if ($className) {
            $buildPath     = $this->getBuildPath();
            $renames       = [
                $buildPath . DIRECTORY_SEPARATOR . $relativePath,
                $buildPath . DIRECTORY_SEPARATOR . 'vendor/autoload.php'
            ];
            $generatedName = 'ComposerAutoloaderInit' . str_random();
            foreach ($renames as $path) {
                $content     = $this->files->get($path);
                $replacement = str_replace($className, $generatedName, $content);
                $this->files->put($path, $replacement);
            }
        }
        return $this;
    }

    /**
     * copying all stubs defined in configuration
     * 
     * @return \Antares\Updater\Strategy\Sandbox\Terminator
     */
    protected function copyStubs()
    {
        $config    = config('antares/updater::sandbox.files.stubs');
        $stubs     = __DIR__ . '/../../../resources/stub';
        $buildPath = $this->getBuildPath();
        foreach ($config as $source => $target) {
            $from = $stubs . DIRECTORY_SEPARATOR . $source;
            if ($this->files->exists($from)) {
                $to = $buildPath . DIRECTORY_SEPARATOR . $target;
                if (!$this->files->isDirectory(dirname($to))) {
                    $this->files->makeDirectory(dirname($to), 0755, true, true);
                }
                $this->files->copy($from, $buildPath . DIRECTORY_SEPARATOR . $target);
            }
        }
        return $this;
    }

    /**
     * getting classname by file content
     * 
     * @param String $path
     * @return String
     */
    protected function getClassnameByFilePath($path)
    {
        $content    = $this->files->get(base_path($path));
        $tokens     = token_get_all($content);
        $classToken = false;
        $className  = '';
        foreach ($tokens as $token) {
            if (is_array($token)) {
                if ($token[0] == T_CLASS) {
                    $classToken = true;
                } else if ($classToken && $token[0] == T_STRING) {
                    $className  = $token[1];
                    $classToken = false;
                }
            }
        }
        return $className;
    }

    /**
     * content parser
     * 
     * @param String $content
     * @return String
     */
    protected function parseContent($content)
    {
        $config                                             = config('database');
        $default                                            = array_get($config, 'default');
        $configPath                                         = $this->getBuildPath() . '/resources/config/database.php';
        $databaseConfig                                     = require $configPath;
        $databaseConfig['connections']['mysql']['database'] = $this->databaseName();
        file_put_contents($configPath, "<?php

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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */




\n return\n\n " . var_export($databaseConfig, true) . ";\n");

        preg_match("'DB_DATABASE=(.*?)\n'si", $content, $match);
        if (!isset($match[1])) {
            throw new Exception('Unable to read valid configuration primary database settings.');
        }
        return str_replace($match[1], $this->databaseName(), $content);
    }

    /**
     * ending sandbox mode creator
     * 
     * @return String
     */
    public function done()
    {
        $version = str_replace('_', '.', $this->getVersion());
        $this->model->create([
            'version' => $version,
            'path'    => $this->getBuildPath()
        ]);

        return ['url' => handles('antares::updater/update', ['csrf' => true, 'sandbox' => $version])];
    }

}
