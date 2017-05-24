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

namespace Antares\Installation\Http\Controllers;

use Antares\Foundation\Http\Controllers\BaseController;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Antares\Installation\Progress;

class ProgressController extends BaseController
{

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupMiddleware()
    {
        
    }

    /**
     * @param Progress $progress
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Progress $progress)
    {

        app('antares.asset')->container('antares/foundation::scripts')
                ->add('codemirror-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/codemirror.min.js')
                ->add('codemirror-javascript-js', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/mode/javascript/javascript.min.js')
                ->add('codemirror-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/codemirror.css')
                ->add('ambiance-css', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.26.0/theme/ambiance.css')
                ->add('installer-js', '/packages/core/js/installer.js');



        set_meta('title', 'Install Antares');
        $progress->start();

        return view('antares/installer::progress', compact('consoleTheme'));
    }

    /**
     * @param Progress $progress
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Progress $progress)
    {
        $converter = new AnsiToHtmlConverter();
        $console   = $progress->getOutput();


        $percentageProgress = $progress->getPercentageProgress();

        if ($percentageProgress === 0) {
            // Fake progress for composer installation.
            $percentageProgress = 7;
        }

        if ($progress->isFailed()) {
            $progress->reset();

            return response()->json([
                        'progress' => $percentageProgress,
                        'redirect' => handles('antares::install/failed'),
            ]);
        }

        if ($progress->isFinished()) {
            $progress->reset();

            return response()->json([
                        'progress' => $percentageProgress,
                        'redirect' => handles('antares::install/completed'),
            ]);
        }

        return response()->json([
                    'progress' => $percentageProgress,
                    'console'  => $console,
        ]);
    }

    /**
     * @param Progress $progress
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stop(Progress $progress)
    {
        $progress->stop();

        return $this->redirect(handles('antares::install/components'));
    }

}
