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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Contracts\Foundation\Events;

use Illuminate\Support\MessageBag;

interface FormResponseContract {

	/**
	 * Handles the success operation with a given message.
	 *
	 * @param string $message
	 */
	public function onSuccess($message);

	/**
	 * Handles the failed operation with a given message.
	 *
	 * @param string $message
	 */
	public function onFail($message);

	/**
	 * Handles the message bag of the failed validation.
	 *
	 * @param MessageBag $messageBag
	 */
	public function onValidationFailed(MessageBag $messageBag);

}
