<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Events;

use Antares\Contracts\Foundation\Events\EventContract;
use Antares\Contracts\Foundation\Events\FormResponseContract;
use Illuminate\Http\Request;

class SecurityFormSubmitted implements EventContract {

	/**
	 * Form Response instance.
	 *
	 * @var FormResponseContract
	 */
	public $listener;

	/**
	 * HTTP Request instance.
	 *
	 * @var Request
	 */
	public $request;

	/**
	 * Additional array of data.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * SecurityFormSubmitted constructor.
	 * @param FormResponseContract $listener
	 * @param Request $request
	 * @param array $data Additional array of data
	 */
	public function __construct(FormResponseContract $listener, Request $request, array $data = []) {
		$this->listener = $listener;
		$this->request 	= $request;
		$this->data 	= $data;
	}

}
