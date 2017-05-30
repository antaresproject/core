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

namespace Antares\Authorization;

use Antares\Acl\MultisessionAcl;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Antares\Contracts\Auth\Guard;
use Antares\Memory\ContainerTrait;
use Antares\Contracts\Memory\Provider;
use Antares\Contracts\Authorization\Authorization as AuthorizationContract;

class Authorization implements AuthorizationContract
{

    use AuthorizationTrait,
        ContainerTrait;

    /**
     * Acl instance name.
     *
     * @var string
     */
    protected $name;

    /**
     * Construct a new object.
     *
     * @param  \Antares\Contracts\Auth\Guard  $auth
     * @param  string  $name
     * @param  \Antares\Contracts\Memory\Provider|null  $memory
     */
    public function __construct(Guard $auth, $name, Provider $memory = null)
    {
        $this->auth    = $auth;
        $this->name    = $name;
        $this->roles   = new Fluent('roles');
        $this->actions = new Fluent('actions');
        $this->roles->addKeyValuePair(1, 'guest');
        $this->attach($memory);
    }

    /**
     * Bind current ACL instance with a Memory instance.
     *
     * @param  \Antares\Contracts\Memory\Provider  $memory
     *
     * @return void
     *
     * @throws \RuntimeException if $memory has been attached.
     */
    public function attach(Provider $memory = null)
    {

        if ($this->attached() && $memory !== $this->memory) {
            //throw new RuntimeException("Unable to assign multiple Antares\Memory instance.");
        }

        if (!is_null($memory)) {
            $this->setMemoryProvider($memory);
            $this->initiate();
        }
    }

    /**
     * Initiate acl data from memory.
     *
     * @return void
     */
    protected function initiate()
    {
        $name = $this->name;
        $data = ['acl' => [], 'actions' => [], 'roles' => []];
        $data = array_merge($data, $this->memory->get("acl_{$name}", []));
        if (empty($data['roles'])) {
            $roles         = $this->memory->get('acl_antares.roles');
            $data['roles'] = $roles == null ? [] : $roles;
        }

        $this->roles->attachKeyValuePair($data['roles']);
        $this->actions->attachKeyValuePair($data['actions']);

        foreach ($data['acl'] as $id => $allow) {
            list($role, $action) = explode(':', $id);
            $this->assign($role, $action, $allow);
        }
    }

    /**
     * Assign single or multiple $roles + $actions to have access.
     *
     * @param  string|array  $roles      A string or an array of roles
     * @param  string|array  $actions    A string or an array of action name
     * @param  bool          $allow
     *
     * @return $this
     */
    public function allow($roles, $actions, $allow = true, $descriptions = null, $categories = null)
    {
        $this->setAuthorization($roles, $actions, $allow);

        return $this->save($descriptions, $categories);
    }

    /**
     * Verify whether current user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string  $action     A string of action name
     *
     * @return bool
     */
    public function can($action)
    {
        $roles = $this->getUserRoles();
        return $this->checkAuthorization($roles, $action);
    }

    /**
     * Verify whether current or primary user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param $action
     * @return bool
     */
    public function canOrPrimaryCan($action)
    {
        return $this->can($action) || $this->canPrimary($action);
    }

    /**
     * Verify whether primary user has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param $action
     * @return bool
     */
    public function canPrimary($action)
    {
        return app(MultisessionAcl::class)->canPrimary($action);
    }

    /**
     * Verify whether given roles has sufficient roles to access the
     * actions based on available type of access.
     *
     * @param  string|array  $roles      A string or an array of roles
     * @param  string        $action     A string of action name
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function check($roles, $action)
    {
        return $this->checkAuthorization($roles, $action);
    }

    /**
     * Shorthand function to deny access for single or multiple
     * $roles and $actions.
     *
     * @param  string|array  $roles      A string or an array of roles
     * @param  string|array  $actions    A string or an array of action name
     *
     * @return $this
     */
    public function deny($roles, $actions)
    {
        return $this->allow($roles, $actions, false);
    }

    /**
     * Sync memory with acl instance, make sure anything that added before
     * ->with($memory) got called is appended to memory as well.
     *
     * @return $this
     */
    public function sync()
    {
//        $name    = $this->name;
//        $current = $this->memory->get("acl_{$name}");
//        $same    = array_get($current, 'actions') == $this->actions->get() && array_get($current, 'roles') == $this->roles->get() && array_get($current, 'acl') == $this->acl;
//        if (!$same && !in_array($name, ['app', 'antares/licensing'])) {
//            $this->memory->put("acl_{$name}", [
//                'acl'     => $this->acl,
//                'actions' => $this->actions->get(),
//                'roles'   => $this->roles->get(),
//            ]);
//        }
//        return $this;
    }

    /**
     * Saves authorization changes
     * 
     * @return \Antares\Authorization\Authorization
     */
    public function save($descriptions = null, $categories = null)
    {
        if ($this->attached()) {
            $name = $this->name;
            $this->memory->put("acl_{$name}", [
                'acl'          => $this->acl,
                'actions'      => $this->actions->get(),
                'roles'        => $this->roles->get(),
                'descriptions' => $descriptions,
                'categories'   => $categories
            ]);
        }
        return $this;
    }

    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Forward call to roles or actions.
     *
     * @param  string  $type           'roles' or 'actions'
     * @param  string  $operation
     * @param  array   $parameters
     *
     * @return \Antares\Authorization\Fluent
     */
    public function execute($type, $operation, array $parameters = [])
    {
        return call_user_func_array([$this->{$type}, $operation], $parameters);
    }

    /**
     * Magic method to mimic roles and actions manipulation.
     *
     * @param  string  $method
     * @param  array   $parameters
     *
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        list($type, $operation) = $this->resolveDynamicExecution($method);

        $response = $this->execute($type, $operation, $parameters);

        if ($operation === 'has') {
            return $response;
        }

        return $this->sync();
    }

    /**
     * Dynamically resolve operation name especially to resolve attach and
     * detach multiple actions or roles.
     *
     * @param  string  $method
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function resolveDynamicExecution($method)
    {
        $method  = Str::snake($method, '_');
        $matcher = '/^(add|rename|has|get|remove|fill|attach|detach)_(role|action)(s?)$/';

        if (!preg_match($matcher, $method, $matches)) {
            throw new InvalidArgumentException("Invalid keyword [$method]");
        }

        $type      = $matches[2] . 's';
        $multiple  = (isset($matches[3]) && $matches[3] === 's');
        $operation = $this->resolveOperationName($matches[1], $multiple);

        return [$type, $operation];
    }

    /**
     * Dynamically resolve operation name especially when multiple
     * operation was used.
     *
     * @param  string  $operation
     * @param  bool    $multiple
     *
     * @return string
     */
    protected function resolveOperationName($operation, $multiple = true)
    {
        if (!$multiple) {
            return $operation;
        } elseif (in_array($operation, ['fill', 'add'])) {
            return 'attach';
        }

        return 'detach';
    }

}
