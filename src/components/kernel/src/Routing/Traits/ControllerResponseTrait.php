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


namespace Antares\Routing\Traits;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait ControllerResponseTrait
{

	/**
	 * Queue notification and redirect.
	 *
	 * @param string $to
	 * @param string|null $message
	 * @param string $type
	 * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
    public function redirectWithMessage($to, $message = null, $type = 'success')
    {
        return redirect_with_message($to, $message, $type);
    }

	/**
	 * Redirect with input and errors.
	 *
	 * @param string $to
	 * @param \Illuminate\Support\MessageBag|array $errors
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
    public function redirectWithErrors($to, $errors)
    {
        return redirect_with_errors($to, $errors);
    }

	/**
	 * Get an instance of the redirector.
	 *
	 * @param  string|null  $to
	 * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
	 */
    public function redirect($to)
    {
        return redirect($to);
    }

    /**
     * Halt current request using App::abort().
     *
     * @param  int     $code
     * @param  string  $message
     * @param  array   $headers
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function suspend($code, $message = '', array $headers = [])
    {
        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }

}
