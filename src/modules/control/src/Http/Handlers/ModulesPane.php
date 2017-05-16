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



namespace Antares\Control\Http\Handlers;

use Antares\Foundation\Http\Composers\LeftPane;

class ModulesPane extends LeftPane
{

    /**
     * @var ModulesPane 
     */
    private static $oInstance = false;

    /**
     * @return ModulesPane
     */
    public static function getInstance()
    {
        if (self::$oInstance == false) {
            self::$oInstance = new self();
        }
        return self::$oInstance;
    }

    /**
     * make resource instance of left pane
     */
    public function make()
    {
        $extensions = app('antares.memory')->make('component')->get('extensions.active');
        $data       = [
            [
                'name'      => 'core_platform',
                'full_name' => 'Core Platform',
                'handles'   => handles('antares::control/acl')
            ]
        ];
        foreach ($extensions as $extension) {
            $name = $extension['name'];
            if ($name == 'control') {
                continue;
            }
            $data[] = array_merge($extension, ['handles' => handles('antares::control/acl', ['name' => "antares/{$name}"])]);
        }
        $this->widget->make('pane.left')->add('modules')->content(view("antares/control::partial._modules", ['data' => $data]));
    }

}
