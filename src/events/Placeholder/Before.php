<?php

namespace Antares\Events\Placeholder;

use Antares\Foundation\Events\AbstractEvent;

class Before extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Placeholder: Before';

    /** @var string */
    protected static $description = 'Runs before placeholder is rendered';

    /** @var string */
    public $placeholderName;

    /** @var  */
    public $values;

    /**
     * Before constructor
     *
     * @param string    $placeholderName
     * @param Blueprint $values
     */
    public function __construct(string $placeholderName, $values)
    {
        $this->placeholderName = $placeholderName;
        $this->values = $values;

        parent::__construct();
    }

}
