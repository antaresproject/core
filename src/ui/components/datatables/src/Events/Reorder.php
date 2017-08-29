<?php

namespace Antares\Datatables\Events;

use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class Reorder
{

    use SerializesModels;

    /**
     * Datatable query model classname
     *
     * @var String
     */
    protected $model;

    /**
     * Datatable classname
     *
     * @var String 
     */
    protected $datatable;

    /**
     * Data container
     *
     * @var array 
     */
    protected $data;

    /**
     * Construct
     * 
     * @param String $model
     * @param String $datatable
     * @param array $data
     */
    public function __construct($model, $datatable, array $data = [])
    {
        $this->datatable = $datatable;
        $this->model     = $model;
        $this->data      = $data;
    }

    /**
     * Model instance getter
     * 
     * @return Model
     */
    public function model()
    {
        return app($this->model);
    }

    /**
     * Datatable instance getter
     * 
     * @return DataTable
     */
    public function datatable()
    {
        return app($this->datatable);
    }

    /**
     * Data container getter
     * 
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

}
