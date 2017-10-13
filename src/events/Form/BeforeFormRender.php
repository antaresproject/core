<?php

namespace Antares\Events\Form;

use Antares\Html\Form\Grid;
use Antares\Foundation\Events\AbstractEvent;

class BeforeFormRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Before form rendered';

    /** @var string */
    protected static $description = 'Runs before form is rendered';

    /** @var Grid */
    public $grid;

    /**
     * BeforeFormRender constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        parent::__construct();
    }

}
