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
use Antares\Updater\Contracts\UpdatePresenter as PresenterContract;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Foundation\Application;
use Antares\Updater\Contracts\FilesProcessor;
use Antares\Updater\Contracts\UpdateListener;
use Antares\Updater\Contracts\Resolver;
use Antares\Support\Facades\Foundation;
use Antares\Support\Facades\Memory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class UpdateProcessor extends Processor
{

    /**
     * resolver instance
     *
     * @var Resolver
     */
    protected $resolver;

    /**
     * kernel console handler
     *
     * @var KernelContract
     */
    protected $kernelConsole;

    /**
     * application handler
     *
     * @var Application
     */
    protected $app;

    /**
     * files processor contract 
     *
     * @var FilesProcessor
     */
    protected $filesProcessor;

    /**
     * constructing
     * 
     * @param Application $app
     * @param Resolver $resolver
     * @param FilesProcessor $filesProcessor
     */
    public function __construct(Application $app, Resolver $resolver, FilesProcessor $filesProcessor, PresenterContract $presenter)
    {

        $this->app            = $app;
        $this->resolver       = $resolver;
        $this->filesProcessor = $filesProcessor;
        $this->presenter      = $presenter;
    }

    /**
     * start installation of system upgrade
     * 
     * @param UpdateListener $listener
     * @param String $version
     * @return \Illuminate\View\View
     */
    public function start(UpdateListener $listener, $version)
    {

        $resolver = $this->validate($version);
        if (!$resolver->isValid()) {
            $messages = $this->presenter->failed($resolver->getMessages());
            return $listener->failed($messages);
        } else {
            ini_set('max_execution_time', 300);
            /** migrating database * */
            $this->resolver->migrate();
            $data     = [
                'migration' => $this->resolver->getMessages(),
                'has_error' => $this->resolver->hasError()
            ];
            /** migrating files * */
            $hasError = (!$data['has_error']) ? (!$this->filesProcessor->process($this->resolver->getPath())) : $data['has_error'];
            if (!$hasError) {
                $hasError = !$this->updateVersion($version, $this->filesProcessor->getMigrationList());
            }

            array_set($data, 'files', $this->filesProcessor->getNotes());
            if ($hasError) {
                $data = $this->presenter->failed(array_merge($data['files'], $data['migration']));
                return $listener->failed($data);
            }
            return $listener->success($this->presenter->success($version, $hasError, $data));
        }
    }

    /**
     * updating actual system version
     * 
     * @param String $version
     * @param array $migrationList
     * @return mixed
     */
    protected function updateVersion($version, array $migrationList)
    {
        DB::beginTransaction();
        $hasError = false;
        try {
            $model                = Foundation::make('Antares\Updater\Model\Version');
            $forUpdate            = $model->query()->where('is_actual', 1)->first();
            $forUpdate->is_actual = 0;
            if (!$forUpdate->save()) {
                throw new Exception('Unable to reset system version.');
            }
            $data     = $this->app->make('antares.version')->getAdapter()->retrive();
            $insert   = array_merge($data, ['db_version' => $version, 'app_version' => $version, 'last_update_date' => Carbon::now()->toDateTimeString(), 'is_actual' => 1]);
            $eloquent = $model->newInstance($insert);
            if (!$eloquent->save()) {
                throw new Exception('Unable to update system version.');
            }
            $sandbox = Foundation::make('Antares\Updater\Model\Sandbox');
            $sandbox->create([
                'version' => $version,
                'path'    => base_path('builds/build_' . str_replace(['.', ','], '_', $version)),
                'files'   => serialize($migrationList)
            ]);
            Memory::make('primary')->push('sandbox.mode', $version);
        } catch (Exception $ex) {
            Log::emergency($ex);
            $hasError = true;
        }
        if ($hasError) {
            DB::rollback();
        } else {
            DB::commit();
        }
        return !$hasError;
    }

    /**
     * validate migration file
     */
    protected function validate($version)
    {
        $adapter = $this->app->make('antares.version')->getAdapter();
        $adapter->retrive();
        return $this->resolver->setPath($adapter->getPath())->setVersion($version)->resolve();
    }

}
