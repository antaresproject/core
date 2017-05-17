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


namespace Antares\Foundation\Processor\Extension;

use Antares\Contracts\Extension\Factory;
use Antares\Foundation\Http\Presenters\Module as Presenter;
use Antares\Contracts\Extension\Listener\Migrator as Listener;
use Illuminate\Support\Fluent;

class ModuleViewer
{

    /**
     * The extension implementation.
     *
     * @var \Antares\Contracts\Extension\Factory
     */
    protected $extension;

    /**
     * The presenter implementation.
     *
     * @var \Antares\Foundation\Http\Presenters\Extension
     */
    protected $presenter;

    /**
     * Construct a new processor.
     *
     * @param \Antares\Foundation\Http\Presenters\Module $presenter
     * @param \Antares\Contracts\Extension\Factory  $extension
     */
    public function __construct(Presenter $presenter, Factory $extension)
    {
        $this->presenter = $presenter;
        $this->extension = $extension;
    }

    /**
     * View modules page.
     *
     * @param  \Antares\Contracts\Extension\Listener\Viewer $listener
     *
     * @return mixed
     */
    public function index(Listener $listener, $category = null)
    {
        $presenter = $this->presenter->setCategory($category);
        $modules   = $presenter->modules();
        return $listener->show(compact('modules', 'category'));
    }

    /**
     * resolving module category name by module real name
     * 
     * @param Fluent $module
     * 
     * @return String
     */
    public function resolveModuleCategoryName(Fluent $module)
    {
        return head(explode('/', $module->name));
    }

}
