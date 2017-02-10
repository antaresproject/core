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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Http\Controllers;

use Antares\Licensing\Processor\LicenseProcessor as Processor;
use Antares\Foundation\Http\Controllers\BaseController;
use Antares\Licensing\Contracts\LicenseListener;
use Illuminate\Http\Request;

class LicenseController extends BaseController implements LicenseListener
{

    /**
     * implments instance of controller
     * 
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }

    public function setupMiddleware()
    {
        ;
    }

    /**
     * checks whether license is valid
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return $this->processor->index($this, $request);
    }

    /**
     * shows information about invalid license
     * 
     * @param array $license
     * @return \Illuminate\View\View
     */
    public function invalid(array $license)
    {
        return view('antares/licensing::license.invalid', compact('license'));
    }

}
