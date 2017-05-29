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

namespace Antares\Datatables\Session;

use Illuminate\Routing\Router;
use Illuminate\Http\Request;

class Session
{

    /**
     * Router instance
     *
     * @var Router
     */
    protected $router;

    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * Construct
     * 
     * @param Router $router
     * @param Request $request
     */
    public function __construct(Router $router, Request $request)
    {
        $this->router  = $router;
        $this->request = $request;
    }

    /**
     * Get key from session
     * 
     * @param String $key
     * @param mixed $perPage
     * @return mixed
     */
    protected function getFromSession($key, $perPage)
    {
        $session = $this->request->session();
        if (!$session->has($key) or $this->request->ajax()) {
            $session->put($key, $perPage);
            $session->save();
        }
        return $session->get($key);
    }

    /**
     * Gets session key
     * 
     * @param type $classname
     * @return type
     */
    protected function getSessionKey($classname)
    {
        return strtolower(implode('-', [class_basename(get_called_class()), class_basename($classname), uri()]));
    }

}
