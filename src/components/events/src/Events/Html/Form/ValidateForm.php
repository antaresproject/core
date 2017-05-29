<?php

namespace Antares\Events\Html\Form;

use Antares\Events\Events\AbstractEvent;

class ValidateForm extends AbstractEvent
{

	public $grid;

	public function __construct($grid, $params = null)
	{
		parent::__construct($params);
		$this->grid = grid;
	}

	public function setName()
	{
		$this->name = trans('antares/events::events.html.form.validate_form');
	}

	public function setDescription()
	{
		$this->description = trans('antares/events::events.html.form.validate_form_description');
	}


}