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






namespace Antares\Updater\Processor;

use Antares\Foundation\Processor\Processor;
use Antares\Updater\Contracts\SandboxPresenter as PresenterContract;
use Antares\Updater\Contracts\FilesProcessor;
use Antares\Updater\Contracts\ModuleListener;
use Antares\Support\Facades\Foundation;
use Antares\Updater\Contracts\Resolver;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Exception;

class ModuleProcessor extends Processor
{

    /**
     * files processor contract 
     *
     * @var FilesProcessor
     */
    protected $filesProcessor;

    /**
     * resolver instance
     *
     * @var Resolver
     */
    protected $resolver;

    /**
     *
     * @var type 
     */
    protected $fileSystem;

    /**
     * constructing
     * 
     * @param PresenterContract $presenter
     * @param FilesProcessor $filesProcessor
     * @param Resolver $resolver
     * @param Filesystem $fileSystem
     */
    public function __construct(PresenterContract $presenter, FilesProcessor $filesProcessor, Resolver $resolver, Filesystem $fileSystem)
    {
        ini_set('max_execution_time', 300);
        $this->presenter      = $presenter;
        $this->filesProcessor = $filesProcessor;
        $this->resolver       = $resolver;
        $this->fileSystem     = $fileSystem;
    }

    /**
     * process module update action
     * 
     * @param String $name
     * @param String $version
     * @param ModuleListener $listener
     * @return \Illuminate\View\View
     */
    public function update($name, $version, ModuleListener $listener)
    {
        try {
            $module = Foundation::make('antares.version')->getAdapter()->retriveModule($name, $version);
            if (is_null($module) or ! array_key_exists('update', $module)) {
                throw new Exception('Module update detauls cannot be resolved. ');
            }
            $resolver = $this->resolver;
            $url      = $module['update'];
            $resolver->setPath($url);
            $resolver->resolve()->migrate();
            if ($resolver->hasError()) {
                return $listener->failed($this->resolver->getMessages());
            }

            $path     = $resolver->getPath();
            /** migrating files * */
            $hasError = !$this->filesProcessor->process($path);

            if ($hasError) {
                return $listener->failed($this->filesProcessor->getNotes());
            }
            $this->updateModule($path);
        } catch (Exception $e) {
            return $listener->failed($e->getMessage());
        }

        return $listener->success();
    }

    /**
     * updating module configuration
     * 
     * @param String $path
     * @return boolean
     */
    protected function updateModule($path)
    {
        return DB::transaction(function() use($path) {
                    $files    = $this->fileSystem->allFiles($path);
                    $manifest = [];
                    foreach ($files as $file) {
                        if ($file->getFilename() == 'manifest.json') {
                            $manifest = (array) json_decode(file_get_contents($file->getRealPath()));
                            break;
                        }
                    }
                    if (!empty($manifest)) {
                        $model = Foundation::make('antares.component')->where('name', array_get($manifest, 'name'))->first();
                        $model->fill($manifest);
                        $model->save();

                        $model->config->fill($this->prepareConfig($manifest));
                        $model->config->save();
                    }
                });
    }

    /**
     * prepare data for module config
     * 
     * @param array $manifest
     * @return array
     */
    protected function prepareConfig(array $manifest)
    {
        return [
            'provides' => implode(';', array_get($manifest, 'provides')),
            'handles'  => implode(';', array_get($manifest, 'handles', [])),
            'autoload' => implode(';', array_get($manifest, 'autoload', [])),
        ];
    }

}
