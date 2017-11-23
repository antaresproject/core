<?php

namespace Antares\Events\Placeholder;

use Antares\Foundation\Events\AbstractEvent;

class After extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Placeholder: After';

    /** @var string */
    protected static $description = 'Runs after placeholder is rendered';

    /** @var string */
    public $placeholderName;

    /** @var  */
    public $values;

    /**
     * After constructor
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
