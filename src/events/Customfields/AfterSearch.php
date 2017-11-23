<?php

namespace Antares\Events\Customfields;

use Antares\Foundation\Events\AbstractEvent;

class AfterSearch extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Custom Fields: After search';

    /** @var string */
    protected static $description = 'Runs after custom fields search is being executed';

    /** @var mixed */
    public $return;

    /**
     * AfterSearch constructor
     *
     * @param mixed $return
     */
    public function __construct(&$return)
    {
        $this->return = $return;

        parent::__construct();
    }

}
