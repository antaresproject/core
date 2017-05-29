<?php

namespace Antares\Html\Events;

use Antares\Events\Events\AbstractEvent;

class FormReady extends AbstractEvent
{

    public $form;

    /**
     * FormReady constructor.
     * @param $form
     */
    public function __construct($form)
    {
        parent::__construct($form);
        $this->form = $form;
    }

    public function setName()
    {
        $this->name = trans('antares/foundation::events.form_ready');
    }

    public function setDescription()
    {
        $this->description = trans('antares/foundation::events.form_ready_description');
    }


}