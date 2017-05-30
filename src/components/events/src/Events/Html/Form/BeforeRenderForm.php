<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Html\Events;

use Antares\Events\Events\AbstractEvent;

class BeforeRenderForm extends AbstractEvent
{
	public $grid;

	public function __construct($grid, $params = null)
	{
		parent::__construct($params);
		$this->grid = grid;
	}

	public function setName()
	{
		$this->name = trans('antares/events::events.html.form.before_render');
	}

	public function setDescription()
	{
		$this->description = trans('antares/events::events.html.form.before_render_description');
	}

}
