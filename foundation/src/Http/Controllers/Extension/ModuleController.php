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

/** listeners * */
use Antares\Contracts\Extension\Listener\Activator as ActivatorListener;
use Antares\Contracts\Extension\Listener\Deactivator as DeactivatorListener;
use Antares\Contracts\Extension\Listener\Migrator as MigratorListener;
/** processors * */
use Antares\Foundation\Processor\Extension\ModuleViewer as Processor;
use Antares\Extension\Processor\Deactivator as DeactivatorProcessor;
use Antares\Extension\Processor\Uninstaller as UninstallerProcessor;
use Antares\Extension\Processor\Activator as ActivatorProcessor;
use Antares\Extension\Processor\Delete as DeleteProcessor;
use Antares\Extension\Processor\Migrator as MigratorProcessor;
/** others * */
use Antares\Extension\Processor\NameSpacer;
use Antares\Support\Facades\Publisher;
use Illuminate\Support\Fluent;

class ModuleController extends Controller implements ActivatorListener, DeactivatorListener, MigratorListener
{

    /**
     * implemenation of processor
     * @var \Antares\Foundation\Processor\Extension\ModuleViewer 
     */
    protected $processor;

    /**
     * constructing
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        $this->middleware('antares.auth');
        $this->middleware('antares.manage');

        $this->middleware('antares.can::modules-list', ['only' => ['index'],]);
        $this->middleware('antares.can::module-details', ['only' => ['show'],]);
        $this->middleware('antares.can::module-activate', ['only' => ['prepare', 'activate'],]);
        $this->middleware('antares.can::module-deactivate', ['only' => ['prepare', 'deactivate'],]);
        $this->middleware('antares.can::module-migrate', ['only' => ['prepare', 'migrate'],]);
        $this->middleware('antares.can::module-uninstall', ['only' => ['prepare', 'uninstall'],]);
        $this->middleware('antares.can::module-delete', ['only' => ['prepare', 'delete'],]);
    }

    /**
     * List all available modules.
     * 
     * GET (:antares)/modules
     * 
     * @return mixed
     */
    public function index($category = null)
    {
        app('antares.extension')->detect();
        set_meta('title', trans('antares/foundation::title.modules.list_breadcrumb'));
        return $this->processor->index($this, $category);
    }

    /**
     * all modules without separation per category
     * 
     * GET (:antares)/modules
     * 
     * @param array $data
     * @return mixed
     */
    public function show(array $data = null)
    {
        $suffix = isset($data['category']) && !empty($data['category']) ? $data['category'] : 'all';
        set_meta('title', trans("antares/foundation::title.modules.{$suffix}"));
        return view('antares/foundation::modules.show', $data);
    }

    /**
     * -----------------------------------ACTIVATION----------------------------------
     */

    /**
     * preparing module package before activation
     * 
     * @param String $category
     * @param String $vendor
     * @param String $package
     * @param String $type
     * @return mixed
     */
    public function prepare($category, $vendor, $package = null, $type = null)
    {
        NameSpacer::getInstance($category, $package, $type)->rewrite();
        return $this->redirect(handles("antares::modules/{$category}/{$vendor}/{$package}/{$type}", ['csrf' => true]));
    }

    /**
     * Activate module.
     *
     * GET (:antares)/modules/(:category)/(:name)/activate
     *
     * @param  \Antares\Extension\Processor\Activator  $activator
     * @param  string  $category
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function activate(ActivatorProcessor $activator, $category, $vendor, $package = null)
    {
        return $activator->activate($this, $this->getExtension($vendor, $package));
    }

    /**
     * Response when module activation has failed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $errors
     *
     * @return mixed
     */
    public function activationHasFailed(Fluent $extension, array $errors)
    {
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when extension activation has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function activationHasSucceed(Fluent $extension)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);
        $message  = trans('antares/foundation::response.modules.activate', $extension->getAttributes());
        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * -----------------------------------DEACTIVATION----------------------------------
     */

    /**
     * Deactivate module.
     *
     * GET (:antares)/modules/(:category)/(:name)/deactivate
     *
     * @param  \Antares\Extension\Processor\Deactivator  $deactivator
     * @param  string  $category
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function deactivate(DeactivatorProcessor $deactivator, $category, $vendor, $package = null)
    {
        return $deactivator->deactivate($this, $this->getExtension($vendor, $package));
    }

    /**
     * Response when module deactivation has succeed.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function deactivationHasSucceed(Fluent $extension)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);

        $message = trans('antares/foundation::response.modules.deactivate', $extension->getAttributes());

        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * -----------------------------------UNINSTALL----------------------------------
     */
    public function uninstall(UninstallerProcessor $uninstaller, $category, $vendor, $package = null)
    {
        $extension   = $this->getExtension($vendor, $package);
        $uninstalled = $uninstaller->uninstall($this, $extension);
        return ($uninstalled) ? $this->uninstallHasSucceed($extension) : $this->uninstallHasFailed($extension, []);
    }

    /**
     * Response when extension uninstall has failed.
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $errors
     * @return mixed
     */
    public function uninstallHasFailed(Fluent $extension, array $errors)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);

        $message = trans('antares/foundation::response.modules.uninstall.error', $extension->getAttributes());
        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message, 'error');
    }

    /**
     * Response when extension uninstall has succeed.
     * 
     * @param  \Illuminate\Support\Fluent  $extension
     * @return mixed
     */
    public function uninstallHasSucceed(Fluent $extension)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);

        NameSpacer::getInstance($category, $extension->get('name'), 'uninstall')->rewrite();

        $message = trans('antares/foundation::response.modules.uninstall.success', $extension->getAttributes());
        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * -----------------------------------MIGRATIONS----------------------------------
     */

    /**
     * Update module, run migration and asset publish command.
     *
     * GET (:antares)/modules/(:category)/(:name)/migrate
     *
     * @param  \Antares\Extension\Processor\Migrator  $migrator
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function migrate(MigratorProcessor $migrator, $category = null, $vendor = null, $package = null)
    {
        return $migrator->migrate($this, $this->getExtension($vendor, $package));
    }

    /**
     * Response when module migration has failed.
     *
     * @param  \Illuminate\Support\Fluent $extension
     * @param  array $errors
     *
     * @return mixed
     */
    public function migrationHasFailed(Fluent $extension, array $errors)
    {
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when module migration has succeed.
     *
     * @param  \Illuminate\Support\Fluent $extension
     *
     * @return mixed
     */
    public function migrationHasSucceed(Fluent $extension)
    {

        $category = $this->processor->resolveModuleCategoryName($extension);

        $message = trans('antares/foundation::response.modules.migrate', $extension->getAttributes());

        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * -----------------------------------DELETE----------------------------------
     */

    /**
     * delete module.
     * 
     * GET (:antares)/modules/(:category)/(:name)/delete
     * 
     * @param \Antares\Foundation\Http\Controllers\Extension\UninstallerProcessor $uninstaller
     * @param \Antares\Foundation\Http\Controllers\Extension\DeleteProcessor $delete
     * @param String $category
     * @param String $vendor
     * @param String $package
     * @return mixed
     */
    public function delete(UninstallerProcessor $uninstaller, DeleteProcessor $delete, $category, $vendor, $package = null)
    {
        return $delete->delete($this, $uninstaller, $this->getExtension($vendor, $package));
    }

    /**
     * Response when module delete has failed.
     * 
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $errors
     * @return mixed
     */
    public function deleteHasFailed(Fluent $extension, array $errors)
    {
        return $this->queueToPublisher($extension);
    }

    /**
     * Response when module delete has succeed.
     * @param  \Illuminate\Support\Fluent  $extension
     * @return mixed
     */
    public function deleteHasSucceed(Fluent $extension)
    {
        $category = $this->processor->resolveModuleCategoryName($extension);
        $message  = trans('antares/foundation::response.modules.delete.success', $extension->getAttributes());
        return $this->redirectWithMessage(handles("antares::modules/{$category}"), $message);
    }

    /**
     * Queue publishing asset to publisher.
     *
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    protected function queueToPublisher(Fluent $extension)
    {
        Publisher::queue($extension->get('name'));

        return $this->redirect(handles('antares::publisher'));
    }

}
