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


namespace Antares\Html\Middleware;

use Antares\Model\Action;
use Closure;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Antares\Contracts\Http\Middleware\ModuleNamespaceResolver;
use Antares\Memory\MemoryManager;
use Antares\Support\Facades\Memory;

class FormMiddleware
{

    /**
     * container instance
     * 
     * @var Container
     */
    protected $container;

    /**
     * resolver instance
     * 
     * @var ModuleNamespaceResolver
     */
    protected $resolver;

    /**
     * memory manager instance
     *
     * @var MemoryManager 
     */
    protected $memory;

    /**
     * Create a new middleware instance.
     * 
     * @param Container $container
     * @param ModuleNamespaceResolver $resolver
     * @param MemoryManager $memory
     */
    public function __construct(Container $container, ModuleNamespaceResolver $resolver, MemoryManager $memory)
    {
        $this->container = $container;
        $this->resolver  = $resolver;
        $this->memory    = $memory->make('collector');
    }

    /**
     * @todo refactoring
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $permission
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $namespace      = $this->resolver->resolve();
        $clear          = $this->resolver->getClear();
        $actionName     = $this->resolver->getAction();
        $controllerName = $this->resolver->getController();

        /** we need to know details about current action * */
        $action = Action::select(['id', 'component_id', 'name'])->where('name', $permission)->whereHas('extension', function($query) use($clear) {
                    $query->where('name', $clear);
                })->first();

        if (is_null($action)) {
            return $next($request);
        }
        /** let's make a better looking of runtime push */
        $pushable = [
            'namespace'    => $namespace,
            'component'    => $clear,
            'controller'   => $controllerName,
            'action'       => $permission,
            'method'       => $actionName,
            'component_id' => $action->component_id,
            'action_id'    => $action->id,
        ];
        Memory::make('runtime')->push('control', $pushable);
        $response = $next($request);

        return $response;
    }

}
