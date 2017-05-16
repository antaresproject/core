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

use Antares\Logger\Contracts\ActivityPresenter as Presenter;
use Antares\Logger\Http\Datatables\ActivityLogs;
use Antares\Logger\Contracts\ActivityListener;
use Antares\Foundation\Processor\Processor;
use Antares\Logger\Repository\Repository;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Antares\Logger\Model\Logs;
use Antares\Model\User;

class ActivityProcessor extends Processor
{

    /**
     * Csv separator
     */
    const separator = ';';

    /**
     * repository instance
     *
     * @var Repository
     */
    protected $repository;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param Repository $repository
     */
    public function __construct(Presenter $presenter, Repository $repository)
    {
        $this->presenter  = $presenter;
        $this->repository = $repository;
    }

    /**
     * default index action
     * 
     * @param String $type
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index($type = null)
    {
        return $this->presenter->table($type);
    }

    /**
     * On delete log item
     * 
     * @param mixed $id
     * @param ActivityListener $listener
     * @return type
     */
    public function delete($id = null, ActivityListener $listener)
    {
        if (is_null($id) && !is_null($range = Input::get('daterange'))) {
            return $this->deleteByRange($listener, $range);
        }
        if (is_null($id) && !is_null($ids = input('attr'))) {
            return $this->deleteByIds($listener, $ids);
        }

        $model = Logs::withoutGlobalScopes()->whereHas('user', function($query) {
                    $query->whereIn('id', $this->users());
                })->find($id);
        if (is_null($model)) {
            return $listener->deleteFailed();
        }
        $model->delete();
        return $listener->deleteSuccess();
    }

    /**
     * Delete activity logs by date range
     * 
     * @param ActivityListener $listener
     * @param array $ids
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function deleteByIds(ActivityListener $listener, array $ids)
    {
        $logs = Logs::query()->whereIn('id', $ids);
        if (!$logs->delete()) {
            return $listener->deleteFailed();
        }
        return $listener->deleteSuccess();
    }

    /**
     * Delete activity logs by date range
     * 
     * @param ActivityListener $listener
     * @param String $range
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function deleteByRange(ActivityListener $listener, $range)
    {
        $start = null;
        $end   = null;
        extract(json_decode($range, true));
        if (is_null($start) or is_null($end)) {
            return $listener->deleteFailed();
        }
        if (!$this->repository->deleteByRange($start, $end)) {
            return $listener->deleteFailed();
        }
        return $listener->deleteSuccess();
    }

    /**
     * shows details about single row log
     * 
     * @param type $id
     * @return type
     */
    public function show($id)
    {
        $model = Logs::withoutGlobalScopes()->with('component', 'priority', 'brand')->with(['user' => function($query) {
                        $query->whereRaw('tbl_users.id in (' . implode(',', $this->users()) . ') or tbl_users.id is null');
                    }])->whereId($id)->firstOrFail();
        return $this->presenter->show($model);
    }

    /**
     * Download actvity log
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download()
    {
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        ignore_user_abort();


        $datatable = app(ActivityLogs::class);

        list($headers, $data) = $this->prepareHeadersAndKeys($datatable);
        $items = $this->prepareContent($datatable, $data);

        $top      = implode(self::separator, $headers);
        $content  = implode("\n", $items);
        $csv      = implode("\n", [$top, $content]);
        $date     = date('Y_m_d_H_i_s', time());
        $filename = "activity_log_{$date}.csv";

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
     * @param ActivityLogs $datatable
     * @param array $data
     * @return array
     */
    protected function prepareContent(ActivityLogs $datatable, array $data)
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
     * Child users getter
     * 
     * @return array
     */
    protected function users()
    {
        $user   = auth()->user();
        $roles  = $childs = $user->roles->first()->getChilds();

        $users = User::select(['id'])->withoutGlobalScopes()->whereHas('roles', function($query) use($roles) {
                    $query->whereIn('tbl_roles.id', array_values($roles));
                })->get()->pluck('id')->toArray();
        array_push($users, $user->id);
        return $users;
    }

}
