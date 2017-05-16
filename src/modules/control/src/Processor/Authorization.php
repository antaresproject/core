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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Processor;

use Antares\Support\Str;
use Illuminate\Support\Fluent;
use Antares\Contracts\Authorization\Factory;
use Antares\Contracts\Foundation\Foundation;

class Authorization extends Processor
{

    /**
     * ACL instance.
     *
     * @var \Antares\Contracts\Authorization\Factory
     */
    protected $acl;

    /**
     * Configuration Repository
     * 
     * @var \Antares\Config\Repository
     */
    protected $config;

    /**
     * Setup a new processor.
     *
     * @param  \Antares\Contracts\Foundation\Foundation  $foundation
     * @param  \Antares\Contracts\Authorization\Factory  $acl
     */
    public function __construct(Foundation $foundation, Factory $acl)
    {
        $this->foundation = $foundation;
        $this->memory     = $foundation->memory();
        $this->acl        = $acl;
        $this->model      = $foundation->make('antares.role');
    }

    /**
     * List ACL collection.
     *
     * @param  object  $listener
     * @param  string  $metric
     *
     * @return mixed
     */
    public function edit($listener, $metric)
    {
        publish('control', ['js/control.js']);
        $collection = [];
        $instances  = $this->acl->all();
        $eloquent   = null;

        foreach ($instances as $name => $instance) {
            $collection[$name] = $this->getAuthorizationName($name);

            $name === $metric && $eloquent = $instance;
        }

        if (is_null($eloquent)) {
            return $listener->aclVerificationFailed();
        }
        $form = $this->formBuilder->create('Antares\Control\Http\Form\Acl', [
            'method' => 'POST',
            'url'    => route('content/save'),
            'model'  => $eloquent
        ]);

        return $listener->indexSucceed(compact('eloquent', 'collection', 'metric', 'form'));
    }

    /**
     * Update ACL metric.
     *
     * @param  object  $listener
     * @param  array   $input
     *
     * @return mixed
     */
    public function update($listener, array $input)
    {
        $all = $this->acl->all();
        if (is_null($all)) {
            return $listener->aclVerificationFailed();
        }
        $roleId = key($input['acl']);

        $role     = $this->foundation->make('antares.role')->query()->where('id', $roleId)->firstOrFail();
        $roleName = $role->name;
        $allowed  = array_keys($input['acl'][$roleId]);

        foreach ($all as $component => $details) {
            $acl     = $this->acl->get($component);
            $actions = $details->actions->get();
            foreach ($actions as $actionId => $name) {
                $allow = in_array($actionId, $allowed);
                $acl->allow($roleName, $name, $allow);
            }
            $acl->save();
        }
        return $listener->updateSucceed($roleId);
    }

    /**
     * Sync role for an ACL instance.
     *
     * @param  object  $listener
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return mixed
     */
    public function sync($listener, $vendor, $package = null)
    {
        $roles = [];
        $name  = $this->getExtension($vendor, $package)->get('name');
        $acl   = $this->acl->get($name);

        if (is_null($acl)) {
            return $listener->aclVerificationFailed();
        }

        foreach ($this->model->all() as $role) {
            $roles[] = $role->name;
        }

        $acl->roles()->attach($roles);

        $acl->sync();

        return $listener->syncSucceed(new Fluent(compact('vendor', 'package', 'name')));
    }

    /**
     * Get extension name (if available).
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getAuthorizationName($name)
    {
        $extension = $this->memory->get("extensions.available.{$name}.name");
        $title     = ($name === 'antares') ? 'Antares' : $extension;

        return (is_null($title) ? Str::title($name) : $title);
    }

    /**
     * Get extension information.
     *
     * @param  string  $vendor
     * @param  string|null  $package
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function getExtension($vendor, $package = null)
    {
        $name = (is_null($package) ? $vendor : implode('/', [$vendor, $package]));

        return new Fluent(['name' => $name, 'uid' => $name]);
    }

}
