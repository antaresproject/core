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

use Antares\Extension\Jobs\ExtensionsBackgroundJob;
use Antares\Extension\Manager;
use Antares\Extension\Processors\Activator;
use Antares\Extension\Processors\Deactivator;
use Antares\Extension\Processors\Installer;
use Antares\Extension\Processors\Progress;
use Antares\Extension\Processors\Uninstaller;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ActionController extends Controller {

    /**
     * @var Manager
     */
    protected $extensionManager;

    /**
     * @var Progress
     */
    protected $progress;

    /**
     * ActionController constructor.
     * @param Manager $extensionManager
     * @param Progress $progress
     */
    public function __construct(Manager $extensionManager, Progress $progress) {
        parent::__construct();

        $this->extensionManager = $extensionManager;
        $this->progress         = $progress;
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware() {
        $this->middleware('antares.auth');
        $this->middleware('antares.manage');
        $this->middleware('antares.csrf');

        $this->canMiddleware('install');
        $this->canMiddleware('uninstall');
        $this->canMiddleware('activate');
        $this->canMiddleware('deactivate');
    }

    /**
     * @param string $operation
     */
    private function canMiddleware(string $operation) {
        $this->middleware('antares.can::component-' . $operation, ['only' => [$operation]]);
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return mixed
     */
    public function install(string $vendor, string $name) {
        return $this->tryRunOperation(Installer::class, $vendor, $name);
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return mixed
     */
    public function uninstall(string $vendor, string $name) {
        return $this->tryRunOperation(Uninstaller::class, $vendor, $name);
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return mixed
     */
    public function activate(string $vendor, string $name) {
        return $this->tryRunOperation(Activator::class, $vendor, $name);
    }

    /**
     * @param string $vendor
     * @param string $name
     * @return mixed
     */
    public function deactivate(string $vendor, string $name) {
        return $this->tryRunOperation(Deactivator::class, $vendor, $name);
    }

    /**
     * @param string $operationClassName
     * @param string $vendor
     * @param string $name
     * @return mixed
     */
    private function tryRunOperation(string $operationClassName, string $vendor, string $name) {
        $this->progress->start();

        $job = new ExtensionsBackgroundJob($vendor . '/' . $name, $operationClassName, $this->progress->getFilePath());
        $job->onQueue('install');

        dispatch($job);

        return response()->json([
            'success' => true,
        ]);
    }

}
