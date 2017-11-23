<?php

namespace Antares\Events\Views;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class BreadcrumbBeforeRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'View: Before render Breadcrumb';

    /** @var string */
    protected static $description = 'Runs before bredcrumb is rendered';

    /** @var mixed */
    public $items;

    /** @var string */
    public $key;

    /**
     * BreadcrumbBeforeRender constructor
     *
     * @param string $key
     * @param mixed  $items
     */
    public function __construct(string $key, $items)
    {
        $this->key = $key;
        $this->items = $items;

        parent::__construct();
    }

}
