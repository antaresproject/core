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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Processor;

use Antares\Updater\Contracts\BackupPresenter as Presenter;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Antares\Updater\Contracts\BackupListener;
use Antares\Foundation\Processor\Processor;
use Antares\Automation\Jobs\ManualLaunch;
use Antares\Automation\Model\JobsQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Updater\Model\Backup;
use Exception;

class BackupProcessor extends Processor
{

    use DispatchesJobs;

    /**
     * JobsQueue instance
     *
     * @var JobsQueue 
     */
    protected $queue;

    /**
     * Construct
     * 
     * @param Presenter $presenter
     * @param JobsQueue $queue
     */
    public function __construct(Presenter $presenter, JobsQueue $queue)
    {
        $this->presenter = $presenter;
        $this->queue     = $queue;
    }

    /**
     * realize index action version controller
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * restoring application from backup
     * 
     * @param BackupListener $listener
     * @param int $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function restore(BackupListener $listener, $id)
    {
        try {
            $eloquent = app(Backup::class)->findOrFail($id);
            $job      = app(ManualLaunch::class)->setCommand('backup:restore ' . $eloquent->name)->onQueue('install');
            $this->dispatch($job);
        } catch (Exception $ex) {
            Log::alert($ex);
            return $listener->restoreFailed($ex->getMessage());
        }
        return $listener->restoreSuccess();
    }

    /**
     * Creates new database backup
     * 
     * @param BackupListener $listener
     * @return \Illuminate\Http\RedirectResponse
     */
    public function create(BackupListener $listener)
    {
        try {
            $backup = Backup::create([
                        'version' => app('antares.version')->getAdapter()->getVersion(),
                        'status'  => 'pending'
            ]);
            $job    = app(ManualLaunch::class)->setCommand('backup:app ' . $backup->id)->onQueue('install');
            $this->dispatch($job);
            return $listener->createSuccess();
        } catch (Exception $ex) {
            Log::alert($ex);
            return $listener->createFailed();
        }
    }

    /**
     * Deletes backup jon from queue
     * 
     * @param BackupListener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(BackupListener $listener, $id)
    {
        DB::beginTransaction();
        try {
            $backup = Backup::query()->findOrFail($id);
            if ($backup->status !== 'pending') {
                return $listener->deleteFailed();
            }
            $jobs = $this->queue->get(['payload'])->filter(function($item) {
                $command = unserialize($item->payload['data']['command']);
                if ($command instanceof ManualLaunch && $command->getCommand() == 'backup:app') {
                    return $item;
                }
                return null;
            });
            foreach ($jobs as $job) {
                $job->delete();
            }
            $backup->delete();
        } catch (Exception $ex) {
            Log::error($ex);
            return $listener->deleteFailed();
        }
        DB::commit();
        return $listener->deleteSuccess();
    }

}
