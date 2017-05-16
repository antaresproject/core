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

use Antares\Updater\Contracts\SandboxPresenter as PresenterContract;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Antares\Updater\Contracts\SessionBroadcaster;
use Antares\Updater\Contracts\SandboxListener;
use Antares\Updater\Contracts\SandboxFiles;
use Antares\Updater\Contracts\Requirements;
use Antares\Foundation\Processor\Processor;
use Antares\Updater\Contracts\Terminator;
use Antares\Updater\Contracts\Rollbacker;
use Antares\Updater\Contracts\Database;
use Antares\Support\Facades\Foundation;

class SandboxProcessor extends Processor
{

    /**
     * Requirements strategy instance
     *
     * @var Requirements 
     */
    protected $requirements;

    /**
     * database copier instance
     *
     * @var Database 
     */
    protected $database;

    /**
     * sandboxfiles instance
     *
     * @var SandboxFiles
     */
    protected $files;

    /**
     * terminator instance
     *
     * @var Terminator
     */
    protected $terminator;

    /**
     * rollbacker instance
     *
     * @var Rollbacker
     */
    protected $rollbacker;

    /**
     * session broadcaster instance
     *
     * @var SessionBroadcaster
     */
    protected $sessionBroadcaster;

    /**
     * kernel console handler
     *
     * @var KernelContract
     */
    protected $kernelConsole;

    /**
     * constructing
     * 
     * @param PresenterContract $presenter
     */
    public function __construct(PresenterContract $presenter, Requirements $requirements, Database $database, SandboxFiles $files, Terminator $terminator, Rollbacker $rollbacker, SessionBroadcaster $sessionBroadcaster, KernelContract $kernelConsole)
    {
        ini_set('max_execution_time', 300);
        $this->presenter          = $presenter;
        $this->requirements       = $requirements;
        $this->database           = $database;
        $this->files              = $files;
        $this->terminator         = $terminator;
        $this->rollbacker         = $rollbacker;
        $this->sessionBroadcaster = $sessionBroadcaster;
        $this->kernelConsole      = $kernelConsole;
    }

    /**
     * process default index action
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * getting requirements to create new sandbox instance
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function requirements(SandboxListener $listener)
    {
        $this->requirements->validate();
        $messages = $this->requirements->getNotes();
        return ($this->requirements->hasError()) ? $listener->failed($messages) : $listener->success($messages);
    }

    /**
     * backup application
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function backup(SandboxListener $listener)
    {
        $this->kernelConsole->call('backup:run');
        $model  = Foundation::make('Antares\Updater\Model\Backup');
        $files  = Foundation::make('Illuminate\Filesystem\Filesystem');
        $target = config('laravel-backup.destination.path');
        $file   = last($files->allFiles(storage_path("app/{$target}")));
        $model->create([
            'name'    => $file->getFilename(),
            'version' => Foundation::make('antares.version')->getAdapter()->getVersion(),
            'path'    => str_replace(base_path(), '', $file->getRealPath())
        ]);

        $messages = explode("\n", $this->kernelConsole->output());
        return $listener->success($messages);
    }

    /**
     * creating new database instance and migrating entities
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function database(SandboxListener $listener)
    {
        $this->database->copy();
        $messages = $this->database->getNotes();
        return ($this->database->hasError()) ? $listener->failed($messages) : $listener->success($messages);
    }

    /**
     * migrate all files from primary system
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function migration(SandboxListener $listener)
    {
        $this->files->copy();
        $messages = $this->files->getNotes();
        return ($this->files->hasError()) ? $listener->failed($messages) : $listener->success($messages);
    }

    /**
     * ending creation sandbox instance
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function ending(SandboxListener $listener)
    {
        $this->terminator->terminate();
        $messages = $this->terminator->getNotes();
        return ($this->terminator->hasError()) ? $listener->failed($messages) : $listener->success($messages);
    }

    /**
     * saving sandbox instance
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function save(SandboxListener $listener)
    {
        $version = str_replace(['_'], '.', $this->database->getVersion());
        $memory  = app('antares.memory')->make('primary');
        $memory->push('sandbox.mode', $version);
        $memory->finish();

        return $listener->success();
    }

    /**
     * opening sandbox instance
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function open(SandboxListener $listener)
    {
        $message = $this->terminator->done();
        return $listener->success($message);
    }

    /**
     * done application creation
     * 
     * @param SandboxListener $listener
     * @return type
     */
    public function done(SandboxListener $listener)
    {
        //$this->sessionBroadcaster->broadcast();
        return $listener->installed();
    }

    /**
     * rollback migration
     * 
     * @param SandboxListener $listener
     * @return \Illuminate\View\View
     */
    public function rollback(SandboxListener $listener)
    {
        $this->rollbacker->rollback();
        $messages = $this->rollbacker->getNotes();
        return ($this->rollbacker->hasError()) ? $listener->failed($messages) : $listener->success($messages);
    }

    /**
     * delete action
     * 
     * @param SandboxListener $listener
     * @param numeric $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(SandboxListener $listener, $id)
    {
        $model = Foundation::make('Antares\Updater\Model\Sandbox')->where('id', $id)->first();
        if (is_null($model)) {
            return $listener->deleteFailed(trans('Sandbox instance has not been deleted. Instance has not been found.'));
        }
        $this->rollbacker->setVersion($model->version)->rollback();
        if ($this->rollbacker->hasError()) {
            $messages = $this->rollbacker->getNotes();
            $failed   = implode('<br/>', $messages);
            return $listener->deleteFailed($failed);
        } else {
            $model->delete();
            return $listener->deleteSuccessfull();
        }
    }

}
