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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Controllers\Admin;

use Antares\Logger\Processor\GeneratorProcessor as Processor;
use Antares\Foundation\Http\Controllers\AdminController;
use Antares\Logger\Contracts\GeneratorListener;

class GeneratorController extends AdminController implements GeneratorListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware("antares.can:antares/logger::report-generate", ['only' => ['generate']]);
        $this->middleware("antares.can:antares/logger::report-download", ['only' => ['download']]);
        $this->middleware("antares.can:antares/logger::report-delete", ['only' => ['delete']]);
        $header = app('request')->header('generator');

        if (is_null($header) or $header != config('app.key')) {
            $this->middleware("antares.can:antares/logger::report-html", ['only' => ['html']]);
            $this->middleware("antares.can:antares/logger::report-view", ['only' => ['view']]);
        }
//        $this->middleware("antares.can:antares/logger::report-generate-standalone", ['only' => ['generateStandalone']]);
    }

    /**
     * default generate action
     * 
     * @return \Illuminate\View\View
     */
    public function generate()
    {
        return $this->processor->generate($this);
    }

    /**
     * when report generation failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateFailed()
    {
        $message = trans('Report has not been created.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * when report generation success
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateSuccess($id)
    {
        $message = trans('Report has been created.');
        app('antares.messages')->add('success', $message);
        return redirect()->to(handles('antares::logger/download/pdf/' . $id));
    }

    /**
     * preview report content
     * 
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function view($id)
    {
        return $this->processor->view($id, $this);
    }

    /**
     * when preview report failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function viewFailed()
    {
        $message = trans('Report not exists.');
        app('antares.messages')->add('error', $message);
        return redirect()->to(handles('antares::logger/'));
    }

    /**
     * download report action
     * 
     * @param String $type
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function download($type, $id)
    {
        return $this->processor->download($type, $id, $this);
    }

    /**
     * when downloading report failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function downloadFailed()
    {
        $message = trans('Error appears while downloading report.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * delete report
     * 
     * @param mixed $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        return $this->processor->delete($id);
    }

    /**
     * when deleting report failed
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFailed()
    {
        $message = trans('Error appears while deleting report.');
        app('antares.messages')->add('error', $message);
        return redirect()->back();
    }

    /**
     * when deleting report completed successfully
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSuccess()
    {
        $message = trans('Report has been deleted.');
        app('antares.messages')->add('success', $message);
        return redirect()->back();
    }

    /**
     * when preview html report
     * 
     * @param mixed $id
     * @return \Illuminate\View\View
     */
    public function html($id)
    {
        return $this->processor->html($id);
    }

    /**
     * when generates standalone system report
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateStandalone()
    {
        return $this->processor->generateStandalone($this);
    }

    /**
     * when downloads report by filename
     * 
     * @param String $filename
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadReport($filename)
    {
        return $this->processor->downloadReportByFilename($this, $filename);
    }

}
