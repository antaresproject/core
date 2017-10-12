<?php

namespace Antares\Events\Eloquent;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;
use Illuminate\Database\Eloquent\Model;

class BeforeFind extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Eloquent: before find statement';

    /** @var string */
    protected static $description = 'Runs before finding model from database';

    /** @var Model */
    public $model;

    /**
     * BeforeFind constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct();
    }

}
