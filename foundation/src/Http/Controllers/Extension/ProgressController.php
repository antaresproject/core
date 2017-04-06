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

use Antares\Extension\Manager;
use Antares\Extension\Processors\Progress;
use Illuminate\Http\Request;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

class ProgressController extends Controller {

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
    public function __construct(Manager $extensionManager) {
        parent::__construct();

        $this->extensionManager = $extensionManager;
        $this->theme = new SolarizedTheme();
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
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        $previewUrl     = route(area() . '.extensions.progress.preview');
        $consoleTheme   = $this->theme->asArray();

        return view('antares/foundation::extensions.progress', compact('previewUrl', 'consoleTheme'));
    }

    /**
     * @param Progress $progress
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Progress $progress) {
        $converter  = new AnsiToHtmlConverter($this->theme);
        $content    = $progress->getOutput();
        $console    = $converter->convert($content);

        return response()->json([
            'console'   => $console,
            'hash'      => bcrypt($content),
            'redirect'  => $progress->isFinished()
                ? route(area() . '.extensions.index')
                : false,
        ]);
    }


}
