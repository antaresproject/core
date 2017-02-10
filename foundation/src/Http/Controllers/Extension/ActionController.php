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


namespace Antares\Foundation\Http\Controllers\Extension;

use Antares\Contracts\Extension\Listener\Activator as ActivatorListener;
use Antares\Contracts\Extension\Listener\Deactivator as DeactivatorListener;
use Antares\Contracts\Extension\Listener\Delete as DeleteListener;
use Antares\Contracts\Extension\Listener\Migrator as MigratorListener;
use Antares\Contracts\Extension\Listener\Uninstaller as UninstallListener;
use Antares\Extension\Processor\Activator as ActivatorProcessor;
use Antares\Extension\Processor\Deactivator as DeactivatorProcessor;
use Antares\Extension\Processor\Delete as DeleteProcessor;
use Antares\Extension\Processor\Migrator as MigratorProcessor;
use Antares\Extension\Processor\Uninstaller as UninstallerProcessor;
use Antares\Support\Facades\Publisher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Fluent;

class ActionController extends Controller implements ActivatorListener, DeactivatorListener, MigratorListener, UninstallListener, DeleteListener
{

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.manage');
        $this->middleware('antares.csrf');

        $this->middleware('antares.can::component-activate', ['only' => ['activate'],]);
        $this->middleware('antares.can::component-migrate', ['only' => ['migrate'],]);
        $this->middleware('antares.can::component-deactivate', ['only' => ['deactivate'],]);
        $this->middleware('antares.can::component-uninstall', ['only' => ['uninstall'],]);
        $this->middleware('antares.can::component-delete', ['only' => ['delete'],]);
    }

    /**
     * Activate an extension.
     *
     * GET (:antares)/extensions/activate/(:name)
     *
     * @param  ActivatorProcessor  $activator
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function activate(ActivatorProcessor $activator, $vendor, $package = null)
    {
        return $activator->activate($this, $this->getExtension($vendor, $package));
    }

    /**
     * Update an extension, run migration and asset publish command.
     *
     * GET (:antares)/extensions/activate/(:name)
     *
     * @param  MigratorProcessor  $migrator
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function migrate(MigratorProcessor $migrator, $vendor, $package = null)
    {
        return $migrator->migrate($this, $this->getExtension($vendor, $package));
    }

    /**
     * Deactivate an extension.
     *
     * GET (:antares)/extensions/deactivate/(:name)
     *
     * @param  DeactivatorProcessor  $deactivator
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function deactivate(DeactivatorProcessor $deactivator, $vendor, $package = null)
    {
        return $deactivator->deactivate($this, $this->getExtension($vendor, $package));
    }

    /**
     * uninstalling an extension.
     * GET (:antares)/extensions/uninstall/(:name)
     * @param  DeactivatorProcessor  $deactivator
     * @param  string  $vendor
     * @param  string|null  $package
     * @return mixed
     */
    public function uninstall(UninstallerProcessor $uninstaller, $vendor, $package = null)
    {
        $extension   = $this->getExtension($vendor, $package);
        $uninstalled = $uninstaller->uninstall($this, $extension);
        return ($uninstalled) ? $this->uninstallHasSucceed($extension) : $this->uninstallHasFailed($extension, []);
    }

    /**
     * delete an extension.
     * GET (:antares)/extensions/delete/(:name)
     * @param UninstallerProcessor $uninstaller
     * @param DeleteProcessor $delete
     * @param  string  $vendor
     * @param  string|null  $package
     * @return mixed
     */
    public function delete(UninstallerProcessor $uninstaller, DeleteProcessor $delete, $vendor, $package = null)
    {
        return $delete->delete($this, $uninstaller, $this->getExtension($vendor, $package));
    }

    /**
     * Response when extension delete has failed.
     * @param  Fluent  $extension
     * @param  array  $errors
     * @return mixed
     */
    public function deleteHasFailed(Fluent $extension, array $errors)
    {
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when extension delete has succeed.
     * @param  Fluent  $extension
     * @return mixed
     */
    public function deleteHasSucceed(Fluent $extension)
    {
        $message = trans('antares/foundation::response.extensions.delete.success', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

    /**
     * Response when extension uninstall has failed.
     * @param  Fluent  $extension
     * @param  array  $errors
     * @return mixed
     */
    public function uninstallHasFailed(Fluent $extension, array $errors)
    {
        $message = trans('antares/foundation::response.extensions.uninstall.error', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message, 'error');
    }

    /**
     * Response when extension uninstall has succeed.
     * @param  Fluent  $extension
     * @return mixed
     */
    public function uninstallHasSucceed(Fluent $extension)
    {
        Event::fire('after.uninstall.' . $extension->name);
        $message = trans('antares/foundation::response.extensions.uninstall.success', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

    /**
     * Response when extension activation has failed.
     *
     * @param  Fluent  $extension
     * @param  array  $errors
     *
     * @return mixed
     */
    public function activationHasFailed(Fluent $extension, array $errors)
    {
        app('antares.messages')->add('error', trans('Component has not been activated. Migration failed.'));
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when extension activation has succeed.
     *
     * @param  Fluent  $extension
     *
     * @return mixed
     */
    public function activationHasSucceed(Fluent $extension)
    {
        Event::fire('after.install.' . $extension->name);
        $message = trans('antares/foundation::response.extensions.activate', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

    /**
     * Response when extension deactivation has succeed.
     *
     * @param  Fluent  $extension
     *
     * @return mixed
     */
    public function deactivationHasSucceed(Fluent $extension)
    {
        $message = trans('antares/foundation::response.extensions.deactivate', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

    /**
     * Response when extension migration has failed.
     *
     * @param  Fluent $extension
     * @param  array $errors
     *
     * @return mixed
     */
    public function migrationHasFailed(Fluent $extension, array $errors)
    {
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when extension migration has succeed.
     *
     * @param  Fluent $extension
     *
     * @return mixed
     */
    public function migrationHasSucceed(Fluent $extension)
    {
        $message = trans('antares/foundation::response.extensions.migrate', $extension->getAttributes());
        return $this->redirectWithMessage(handles('antares::extensions'), $message);
    }

    /**
     * Queue publishing asset to publisher.
     *
     * @param  Fluent  $extension
     *
     * @return mixed
     */
    protected function queueToPublisher(Fluent $extension)
    {
        Publisher::queue($extension->get('name'));
        return $this->redirect(handles('antares::extensions'));
    }

}
