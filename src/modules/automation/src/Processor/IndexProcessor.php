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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Processor;

use Antares\Automation\Contracts\IndexPresenter as Presenter;
use Illuminate\Contracts\Console\Kernel as KernelContract;
use Antares\Automation\Http\Datatables\AutomationLogs;
use Antares\Automation\Contracts\IndexListener;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Antares\Foundation\Processor\Processor;
use Antares\Automation\Jobs\ManualLaunch;
use Antares\Automation\Model\JobResults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Automation\Model\Jobs;
use Illuminate\Http\JsonResponse;
use Antares\Support\Collection;
use Illuminate\View\View;
use Exception;

class IndexProcessor extends Processor
{

    use DispatchesJobs;

    /**
     * Separator for csv items
     */
    const separator = ';';

    /**
     * console instance
     *
     * @var KernelContract
     */
    protected $kernelConsole;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter, KernelContract $kernelConsole)
    {
        $this->presenter     = $presenter;
        $this->kernelConsole = $kernelConsole;
    }

    /**
     * default index action
     * 
     * @return View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * Shows job details
     * 
     * @param mixed $id
     * @param IndexListener $listener
     * @return View
     */
    public function show($id, IndexListener $listener)
    {
        $model = app('Antares\Automation\Model\Jobs')->whereId($id)->first();
        return is_null($model) ? $listener->showFailed() : $this->presenter->tableShow($model);
    }

    /**
     * shows edit form
     * 
     * @param mixed $id
     * @param IndexListener $listener
     * @return View
     */
    public function edit($id, IndexListener $listener)
    {
        $model = app('Antares\Automation\Model\Jobs')->where('id', $id)->first();
        if (is_null($model)) {
            return $listener->showFailed();
        }
        return $this->presenter->edit($model);
    }

    /**
     * update job
     * 
     * @param mixed $id
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function update(IndexListener $listener)
    {
        $id    = Input::get('id');
        $model = app('Antares\Automation\Model\Jobs')->where('id', $id)->firstOrFail();
        $form  = $this->presenter->form($model);
        if (!$form->isValid()) {
            return $listener->updateValidationFailed($id, $form->getMessageBag());
        }
        if (is_null($model)) {
            return $listener->updateFailed();
        }
        $data   = $form->getData();
        $values = unserialize($model->value);
        if (app()->make($values['classname'])->getDisablable()) {
            $model->active = isset($data['active']) ? $data['active'] : 0;
        }

        foreach ($data as $key => $value) {
            $values[$key] = $value;
        }
        $model->value = serialize($values);
        $model->save();
        return $listener->updateSuccess();
    }

    /**
     * runs single job
     * 
     * @param mixed $id
     * @param IndexListener $listener
     * @return View
     */
    public function run($id, IndexListener $listener)
    {
        $model  = app('Antares\Automation\Model\Jobs')->where('id', $id)->first();
        $params = $model->value + array_except($model->toArray(), 'value');

        if (!is_null($classname = array_get($params, 'classname')) and class_exists($classname)) {
            $job = app(ManualLaunch::class)->setCommand($model->name)->onConnection('database')->onQueue('install');
            $this->dispatch($job);
            return $listener->runSuccess();
        }
        return $listener->runFailed();
    }

    /**
     * Downloads automation logs
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download()
    {
        $datatable = app(AutomationLogs::class);

        list($headers, $data) = $this->prepareHeadersAndKeys($datatable);
        $items    = $this->prepareContent($datatable, $data);
        $top      = implode(self::separator, $headers);
        $content  = implode("\n", $items);
        $csv      = implode("\n", [$top, $content]);
        $date     = date('Y_m_d_H_i_s', time());
        $filename = "automation_log_{$date}.csv";

        return response($csv, 200, [
            'Content-Type'              => 'text/csv',
            'Content-Description'       => 'File Transfer',
            'Content-Disposition'       => 'attachment; filename=' . $filename,
            'Content-Transfer-Encoding' => 'binary',
            'Connection'                => 'Keep-Alive',
            'Expires'                   => '0',
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public',
            'Content-Length'            => strlen($csv)]);
    }

    /**
     * Prepares content for csv
     * 
     * @param AutomationLogs $datatable
     * @param array $data
     * @return array
     */
    protected function prepareContent(AutomationLogs $datatable, array $data)
    {
        $collection = $datatable->query()->get();
        $return     = [];
        foreach ($collection as $log) {
            $element = [];
            foreach ($data as $key => $method) {
                if (method_exists($datatable, $method)) {
                    $called = call_user_func($datatable->{$method}(), $log);
                    $value  = is_object($called) ? $called->__toString() : $called;
                } else {
                    $value = $log->{$key};
                }
                if (str_contains($value, "\n")) {
                    $value = '"' . $value . '"';
                }
                array_push($element, strip_tags($value));
            }
            array_push($return, implode(self::separator, $element));
        }
        return $return;
    }

    /**
     * Prepares headers and data keys for csv
     * 
     * @param ActivityLogs $datatable
     * @return array
     */
    protected function prepareHeadersAndKeys($datatable)
    {
        $headers = [];
        $data    = [];
        $columns = $datatable->html()->getColumns();
        foreach ($columns as $column) {
            if ($column->data == 'action') {
                continue;
            }
            array_push($headers, $column->title);
            array_set($data, $column->data, 'get' . ucfirst($column->data) . 'Value');
        }
        return [$headers, $data];
    }

    /**
     * Deletes automation logs
     * 
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function delete(IndexListener $listener)
    {
        if (is_null($daterange = Input::get('daterange'))) {
            return $listener->deleteFailed();
        }
        $start = null;
        $end   = null;
        extract(json_decode($daterange, true));
        if (is_null($start) or is_null($end)) {
            return $listener->deleteFailed();
        }

        $logs = app(JobResults::class)->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 00:00:00']);
        if (!$logs->count()) {
            return $listener->noLogsToDelete();
        }
        DB::beginTransaction();
        try {
            $logs->delete();
        } catch (Exception $ex) {
            Log::alert($ex);
            DB::rollback();
            return $listener->deleteFailed();
        }
        DB::commit();
        return $listener->deleteSuccess();
    }

    /**
     * Gets scripts collection for filter
     * 
     * @return JsonResponse
     */
    public function scripts()
    {

        $input   = Input::all();
        $builder = Jobs::query();
        if (!is_null($query   = array_get($input, 'q'))) {
            $builder->where('name', 'like', '%' . e($query) . '%')->orWhere('value', 'like', '%' . e($query) . '%');
        }
        $collection = $builder->get();
        $return     = new Collection();
        foreach ($collection as $collected) {
            $return->push(['id' => $collected->id, 'script_name' => unserialize($collected->value)['title']]);
        }

        $paginate = $return->forPage(array_get($input, 'page', 0), array_get($input, 'per_page', 20));
        return new JsonResponse(new Paginator($paginate, 20, 0), 200);
    }

}
