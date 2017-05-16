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

namespace Antares\Updater\Http\Presenters;

use Antares\Updater\Contracts\IndexPresenter as PresenterContract;
use Antares\Updater\Http\Breadcrumb\Breadcrumb;
use Illuminate\Contracts\Container\Container;
use Antares\Updater\Http\Datatables\Versions;
use Antares\Updater\Contracts\Adapter;
use Illuminate\View\View;

class IndexPresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * breadcrumbs instance
     * 
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * versions datatable
     *
     * @var Versions 
     */
    protected $versions;

    /**
     * constructing
     * 
     * @param Container $container
     * @param Breadcrumb $breadcrumb
     * @param Versions $versions
     */
    public function __construct(Container $container, Breadcrumb $breadcrumb, Versions $versions)
    {
        $this->container  = $container;
        $this->breadcrumb = $breadcrumb;
        $this->versions   = $versions;
    }

    /**
     * Table View Generator for Antares\Updater\Version.
     * 
     * @return View
     */
    public function table()
    {
        $this->breadcrumb->onVersionsList();
        return $this->versions->render('antares/updater::admin.index.index');
    }

    /**
     * update page of current version
     * 
     * @param Adapter $adapter
     * @param String $sandbox
     * @return View
     */
    public function update(Adapter $adapter, $sandbox = null)
    {
        $this->breadcrumb->onSystemVersion();
        publish('updater', ['js/update.js']);
        $data           = $adapter->retrive();
        $currentVersion = $adapter->getActualVersion();
        $token          = $this->container->make('session')->token();
        $url            = route('installation/start', ['token' => $data['version'], '_token' => $token, 'sandbox' => $data['version']]);
        $isNewer        = $adapter->isNewer();
        $moduleUpdates  = isset($data['modules']) ? $this->availableModulesUpdate($data['modules']) : [];
        return view('antares/updater::admin.index.update', compact('data', 'currentVersion', 'url', 'isNewer', 'sandbox', 'moduleUpdates'));
    }

    /**
     * get available modules update
     * 
     * @param array $modules
     * @return array
     */
    protected function availableModulesUpdate(array $modules = array())
    {
        $moduleUpdates = [];
        $extensions    = app('antares.memory')->make('component')->get('extensions.active');
        foreach ($extensions as $extenion) {
            foreach ($modules as $module) {
                $currentVersion = array_get($extenion, 'version');
                if (array_get($extenion, 'name') == array_get($module, 'name') && $currentVersion < array_get($module, 'version')) {
                    array_push($moduleUpdates, $module + ['current_version' => $currentVersion]);
                }
            }
        }
        return $moduleUpdates;
    }

}
