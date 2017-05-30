<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Controllers\Extension;

use Antares\Extension\ExtensionProgress;
use Antares\Extension\Manager;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

class ProgressController extends Controller
{

    /**
     * @var Manager
     */
    protected $extensionManager;

    /**
     * @var SolarizedTheme
     */
    protected $theme;

    /**
     * ActionController constructor.
     * @param Manager $extensionManager
     */
    public function __construct(Manager $extensionManager)
    {
        parent::__construct();

        $this->extensionManager = $extensionManager;
        $this->theme            = new SolarizedTheme();
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
        $this->middleware('antares.csrf');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(ExtensionProgress $progress)
    {
        $previewUrl   = route(area() . '.modules.progress.preview');
        $stopUrl      = route(area() . '.modules.progress.stop');
        $consoleTheme = $this->theme->asArray();

        $progress->start();

        return view('antares/foundation::extensions.progress', compact('previewUrl', 'stopUrl', 'consoleTheme'));
    }

    /**
     * @param ExtensionProgress $progress
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(ExtensionProgress $progress)
    {
        $converter = new AnsiToHtmlConverter($this->theme);
        $content   = $progress->getOutput();
        $console   = $converter->convert($content);
        $finished  = $progress->isFinished();

        if ($finished) {
            app('antares.messages')->add('success', $progress->getSuccessMessage());
            $progress->reset();
        }

        if ($progress->isFailed()) {
            app('antares.messages')->add('error', $progress->getFailedMessage());
        }

        return response()->json([
                    'console'  => $console,
                    'redirect' => $finished ? route(area() . '.modules.index') : false,
        ]);
    }

    /**
     * @param ExtensionProgress $progress
     * @return \Illuminate\Http\JsonResponse
     */
    public function stop(ExtensionProgress $progress)
    {
        $progress->stop();

        return response()->json([
                    'success' => true,
        ]);
    }

}
