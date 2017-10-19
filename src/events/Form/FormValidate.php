<?php

namespace Antares\Events\Form;

use Antares\Html\Form\Grid;
use Antares\Foundation\Events\AbstractEvent;

class FormValidate extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form: validate';

    /** @var string */
    protected static $description = 'Runs when form is validated';

    /** @var Grid */
    public $grid;

    /**
     * FormValidate constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        parent::__construct();
    }

}
