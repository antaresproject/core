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



namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\GeneratorPresenter as Presenter;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Antares\Logger\Contracts\GeneratorListener;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Response;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use Antares\Logger\Model\ReportType;
use Illuminate\Http\JsonResponse;
use Antares\Logger\Model\Report;
use Illuminate\View\View;
use SplFileInfo;

class GeneratorProcessor extends Processor
{

    /**
     * console kernel instance
     *
     * @var KernelContract
     */
    protected $consoleKernel;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter, KernelContract $consoleKernel)
    {
        $this->presenter     = $presenter;
        $this->consoleKernel = $consoleKernel;
    }

    /**
     * default generate action
     * 
     * @return View
     */
    public function generate(GeneratorListener $listener)
    {
        $today = date('Y-m-d H:i:s');
        $model = new Report([
            'name'     => "System Analyzer Report {$today}",
            'html'     => Input::get('src'),
            'user_id'  => auth()->user()->id,
            'brand_id' => app('antares.memory')->make('primary')->get('brand.default'),
            'type_id'  => ReportType::where('name', Input::get('type', "analyzer"))->first()->id
        ]);
        if ($model->save()) {
            return $listener->generateSuccess($model->id);
        }
        return $listener->generateFailed();
    }

    /**
     * preview report content
     * 
     * @param mixed $id
     * @param GeneratorListener $listener
     * @return View
     */
    public function view($id, GeneratorListener $listener)
    {
        $model = Report::where('id', $id)->first();
        if (is_null($model)) {
            return $listener->viewFailed();
        }
        return $this->presenter->publishHtml($model);
    }

    /**
     * download report content
     * 
     * @param String $type
     * @param mixed $id
     * @param GeneratorListener $listener
     */
    public function download($type, $id, GeneratorListener $listener)
    {
        $model = Report::where('id', $id)->first();
        if (is_null($model)) {
            return $listener->downloadFailed();
        }
        $filename = snake_case(str_replace(['-', ':', ' '], '_', $model->name));
        switch ($type) {
            case 'pdf':
                $snappy = app('snappy.pdf.wrapper');
                $appKey = config('app.key');
                $ch     = curl_init();
                curl_setopt($ch, CURLOPT_URL, handles('antares::logger/view/' . $id . '/html'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ["generator:  {$appKey}"]);
                $html   = curl_exec($ch);
                curl_close($ch);
                $snappy->loadHTML($html);
                $return = $snappy->download($filename . '.pdf');
                break;
            default:
                $return = Response::make($model->html, '200', array(
                            'Content-Type'        => 'application/octet-stream',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '.html'
                ));
                break;
        }
        return $return;
    }

    /**
     * delete report
     * 
     * @param mixed $id
     * @param GeneratorListener $listener
     * @return View|RedirectResponse
     */
    public function delete($id, GeneratorListener $listener)
    {
        $model = Report::where('id', $id)->first();
        if (is_null($model)) {
            return $listener->deleteFailed();
        }
        if ($model->delete()) {
            return $listener->deleteFailed();
        }
        return $listener->deleteSuccess();
    }

    /**
     * preview report as html content
     * 
     * @param mixed $id
     * @return View
     */
    public function html($id)
    {
        $model = Report::query()->find($id)->first();
        $html  = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $model->html);
        $html  = preg_replace('#<link(.*?)>#is', '', $html);
        return $this->presenter->html($html);
    }

    /**
     * response when generates standalone system report
     * 
     * @return JsonResponse
     */
    public function generateStandalone(GeneratorListener $listener)
    {
        $response    = $this->consoleKernel->call('report:analyzer');
        $splFileInfo = new SplFileInfo($this->consoleKernel->output());
        return new JsonResponse(['redirect' => handles('antares::logger/generate/download/' . trim($splFileInfo->getFilename()))], 200);
    }

    /**
     * downloads system report by filename
     * 
     * @param GeneratorListener $listener
     * @param String $filename
     * @return Response|RedirectResponse
     */
    public function downloadReportByFilename(GeneratorListener $listener, $filename)
    {
        if (!$filename) {
            return $listener->downloadFailed();
        }
        $file       = storage_path('temp' . DIRECTORY_SEPARATOR . $filename);
        /* @var $filesystem Filesystem */
        $filesystem = app(Filesystem::class);
        if (!$filesystem->exists($file)) {
            return $listener->downloadFailed();
        }
        return Response::download($file);
    }

}
