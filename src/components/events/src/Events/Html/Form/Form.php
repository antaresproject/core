<?php

namespace Antares\Events\Html\Form;

use Antares\Events\Events\AbstractEvent;

class Form extends AbstractEvent
{

	public $formName;

	public function __construct($formName, $params = null)
	{
		parent::__construct($params);
		$this->formName = $formName;
	}

	public function setName()
	{
		$this->name = trans('antares/events::events.html.form.form');
	}

	public function setDescription()
	{
		$this->description = trans('antares/events::events.html.form.form_description');
	}


}