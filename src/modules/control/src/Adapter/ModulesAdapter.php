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

namespace Antares\Control\Adapter;

use Antares\Control\Contracts\ModulesAdapter as ModulesAdapterContract;
use Illuminate\Container\Container;

class ModulesAdapter implements ModulesAdapterContract
{

    /**
     * application instance
     *
     * @var Container
     */
    protected $container;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * fetch modules list
     * 
     * @return type
     */
    public function modules()
    {
        $memory        = $this->container->make('antares.memory');
        $configuration = $memory->make('component');
        $extensions    = $configuration->get('extensions.active');
        $coreActions   = $configuration->get('acl_antares.actions');

        $data = [
            [
                'name'      => 'antares',
                'namespace' => 'antares',
                'full_name' => 'Core Platform',
                'actions'   => $coreActions
            ]
        ];
        foreach ($extensions as $extension) {
            $name = $extension['name'];

            $actions = $configuration->get("acl_antares/{$name}.actions");
            $data[]  = array_merge(array_only($extension, ['name', 'full_name', 'description']), [
                'actions'   => $actions,
                'namespace' => "antares/{$name}"
            ]);
        }
        return $data;
    }

    /**
     * find smashed resource
     * 
     * @param String $name
     * @param String $resource
     * @param array $item
     * @param String $controller
     * @param array $return
     * @return array
     */
    protected function smashResource($name, $resource, $item, $controller, &$return)
    {
        $pattern = "{$name}::{$resource}::";
        if (starts_with($item, $pattern)) {
            $id                                               = $this->collector->id($item);
            $actionForm                                       = preg_replace("/{$pattern}/", '', $item);
            $smashed                                          = explode('::', $actionForm);
            return $return[$resource][$controller][$smashed[0]][$id] = $smashed[1];
        }
    }

}
