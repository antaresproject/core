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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Http\Controllers\Admin;

use Antares\UI\UIComponents\Processor\DestroyProcessor as Processor;
use Antares\UI\UIComponents\Contracts\Destroyer as DestroyContract;
use Antares\Foundation\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Response;

class DestroyController extends AdminController implements DestroyContract
{

    /**
     * Implements instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    /**
     * Route acl access controlling
     */
    public function setupMiddleware()
    {
        $this->middleware('antares.auth');
    }

    /**
     * Remove the specified resource from storage.
     * DISABLE /ui-components/{id}
     *
     * @param  mixed  $id
     * @return Response
     */
    public function disable($id)
    {
        return $this->processor->disable($this, $id);
    }

    /**
     * Executes when processor do not destroy a ui component
     */
    public function whenDestroyError(array $errors)
    {
        $message = trans('Ui component has not been deleted. Please try again or just contact with your software provider.', $errors);
        return Response::json(['message' => $message], 302);
    }

    /**
     * Executes when processor destroys a ui component
     */
    public function whenDestroySuccess()
    {
        $message = trans('Ui component has been deleted.');
        return Response::json(['message' => $message], 200);
    }

}
