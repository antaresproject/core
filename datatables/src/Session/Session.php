<?php

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
        return strtolower(implode('-', [class_basename(get_called_class()), class_basename($classname), $this->router->current()->uri()]));
    }

}
