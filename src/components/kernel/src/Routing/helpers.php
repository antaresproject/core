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


if (!function_exists('redirect_with_errors')) {

    /**
     * Redirect with input and errors.
     *
     * @param string $to
     * @param \Illuminate\Support\MessageBag|array $errors
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect_with_errors($to, $errors)
    {
        return redirect($to)->withInput()->withErrors($errors);
    }

}

if (!function_exists('redirect_with_message')) {

    /**
     * Queue notification and redirect.
     *
     * @param string $to
     * @param string|null $message
     * @param string $type
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    function redirect_with_message($to, $message = null, $type = 'success')
    {

        !is_null($message) && app('antares.messages')->add($type, $message);

        return redirect($to);
    }

}