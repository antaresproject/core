<?php

namespace Antares\Events\Eloquent;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;
use Illuminate\Database\Eloquent\Model;

class AfterFind extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Eloquent: after find statement';

    /** @var string */
    protected static $description = 'Runs after finding model from database';

    /** @var Model */
    public $model;

    /**
     * AfterFind constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct();
    }

}
