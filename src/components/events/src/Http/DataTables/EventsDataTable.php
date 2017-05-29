<?php

namespace Antares\Events\Http\DataTables;

use Antares\Datatables\Services\DataTable;
use Antares\Events\Model\Event as EventModel;
use Illuminate\Support\Facades\Event as EventAction;

class EventsDataTable extends DataTable
{

    public function query()
    {
        return app(EventModel::class)->select();
    }

    public function ajax()
    {
        EventAction::listen('datatables.order.namespace', function ($query, $direction) {
            $query->orderBy('namespace', $direction);
        });
        EventAction::listen('datatables.order.fire_count', function ($query, $direction) {
            $query->orderBy('fire_count', $direction);
        });
        EventAction::listen('datatables.order.updated_at', function ($query, $direction) {
            $query->orderBy('updated_at', $direction);
        });

        return $this->prepare()
            ->editColumn('name', function ($model) {
                $obj = new $model->namespace(['showEvents' => true]);
                $obj->setName();
                return $obj->getName();
            })
            ->editColumn('description', function ($model) {
                $obj = new $model->namespace(['showEvents' => true]);
                $obj->setDescription();
                return $obj->getDescription();
            })
            ->editColumn('fire_count', function ($model) {
                return '<span class="badge badge--success">' . $model->fire_count . '</span>';
            })
            ->make(true);
    }

    public function html()
    {
        return $this->setName('Events List')
            ->addColumn(['data' => 'namespace', 'name' => 'namespace', 'title' => trans('antares/events::datagrid.namespace')])
            ->addColumn(['data' => 'name', 'name' => 'name', 'orderable' => false, 'searchable' => false, 'title' => trans('antares/events::datagrid.name')])
            ->addColumn(['data' => 'description', 'name' => 'description', 'orderable' => false, 'searchable' => false, 'title' => trans('antares/events::datagrid.description')])
            ->addColumn(['data' => 'fire_count', 'name' => 'fire_count', 'searchable' => false, 'title' => trans('antares/events::datagrid.fire_count')])
            ->addColumn(['data' => 'updated_at', 'name' => 'updated_at', 'searchable' => false, 'title' => trans('antares/events::datagrid.updated_at')])
            ->setDeferedData();
    }
}
