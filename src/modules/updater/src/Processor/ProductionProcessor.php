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

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Antares\Updater\Contracts\ProductionListener;
use Antares\Foundation\Processor\Processor;
use Antares\Updater\Contracts\Rollbacker;
use Antares\Support\Facades\Foundation;
use Antares\Support\Facades\Memory;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Log;

class ProductionProcessor extends Processor
{

    /**
     * kernel console handler
     *
     * @var KernelContract
     */
    protected $kernelConsole;

    /**
     * container instance
     *
     * @var Container 
     */
    protected $container;

    /**
     * rollbacker instance
     *
     * @var Rollbacker 
     */
    protected $rollbacker;

    /**
     * constructing
     */
    public function __construct(KernelContract $kernelConsole, Rollbacker $rollbacker, Container $container)
    {
        ini_set('max_execution_time', 300);
        $this->kernelConsole = $kernelConsole;
        $this->container     = $container;
        $this->rollbacker    = $rollbacker;
    }

    /**
     * create list of iterations for migration from sandbox to production
     * 
     * @param ProductionListener $listener
     * @return \Illuminate\View\View
     */
    public function iterations(ProductionListener $listener)
    {
        $configration = config('antares/updater::production.process');
        $data         = [];
        $token        = $this->container->make('session')->token();
        $version      = Memory::make('primary')->get('sandbox.mode');

        foreach ($configration as $item) {
            $action = array_get($item, 'action');

            $url = ($action == 'start') ?
                    route("installation/$action", ['token' => $version, '_token' => $token]) :
                    handles("antares::updater/production/{$action}", ['csrf' => true, 'sandbox' => false, 'version' => $version]);

            array_push($data, [
                'url'         => $url,
                'description' => array_get($item, 'description')
            ]);
        }
        return $listener->success($data);
    }

    /**
     * validate whether sandbox version is identical with installed primary version
     * 
     * @param ProductionListener $listener
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function validate(ProductionListener $listener)
    {

        $requestedVersion = app('request')->get('version');
        if (!$requestedVersion) {
            return $listener->failed(['message' => trans('Unable to resolve instance version.')]);
        }
        $actualVersion = app('antares.version')->getAdapter()->getActualVersion();
        if ($requestedVersion == $actualVersion or ! preg_match('/^([0-9]).([0-9]).([0-9])$/', $requestedVersion)) {
            return $listener->failed([
                        'message' => trans('Sandbox version is identical to primary system version.'),
                        'action'  => 'break'
            ]);
        }
        return $listener->success();
    }

    /**
     * backup production application
     * 
     * @param ProductionListener $listener
     * @return \Illuminate\View\View
     */
    public function backup(ProductionListener $listener)
    {
        try {
            $this->kernelConsole->call('backup:run');

            $model    = Foundation::make('Antares\Updater\Model\Backup');
            $files    = Foundation::make('Illuminate\Filesystem\Filesystem');
            $target   = config('laravel-backup.destination.path');
            $file     = last($files->allFiles(storage_path("app/{$target}")));
            $model->create([
                'name'    => $file->getFilename(),
                'version' => Foundation::make('antares.version')->getAdapter()->getVersion(),
                'path'    => str_replace(base_path(), '', $file->getRealPath())
            ]);
            $messages = explode("\n", $this->kernelConsole->output());
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->failed($e->getMessage());
        }
        return $listener->success($messages);
    }

    /**
     * finishing migration process
     * 
     * @param ProductionListener $listener
     * @return \Illuminate\View\View
     */
    public function finish(ProductionListener $listener)
    {
        $memory     = Memory::make('primary');
        $version    = $memory->get('sandbox.mode');
        $rollbacker = $this->rollbacker->setVersion($version);
        $rollbacker->rollback();
        if ($rollbacker->hasError()) {
            $messages = $rollbacker->getNotes();
            return $listener->failed($messages);
        }
        $memory->getHandler()->forceDelete('sandbox.mode');
        Foundation::make('Antares\Updater\Model\Sandbox')->where('version', $version)->first()->delete();
        return $listener->success(['url' => handles('antares::updater/update')]);
    }

    /**
     * rollback migration
     * 
     * @param ProductionListener $listener
     * @return \Illuminate\View\View
     */
    public function rollback(ProductionListener $listener)
    {
        return $listener->success();
    }

}
