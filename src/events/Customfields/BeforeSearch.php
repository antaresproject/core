<?php

namespace Antares\Events\Customfields;

use Antares\Foundation\Events\AbstractEvent;

class BeforeSearch extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Custom Fields: Before search';

    /** @var string */
    protected static $description = 'Runs before custom fields search is being executed (????)';

    /** @var mixed */
    public $return;

    /**
     * BeforeSearch constructor
     *
     * @param mixed $return
     */
    public function __construct($return)
    {
        $this->return = $return;

        parent::__construct();
    }

}
