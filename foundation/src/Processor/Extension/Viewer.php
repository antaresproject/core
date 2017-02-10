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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Processor\Extension;

use Antares\Contracts\Extension\Factory;
use Antares\Foundation\Processor\Processor;
use Antares\Contracts\Extension\Command\Viewer as Command;
use Antares\Foundation\Http\Presenters\Extension as Presenter;

class Viewer extends Processor implements Command
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
     * @param \Antares\Foundation\Http\Presenters\Extension $presenter
     * @param \Antares\Contracts\Extension\Factory  $extension
     */
    public function __construct(Presenter $presenter, Factory $extension)
    {
        $this->extension = $extension;
        $this->presenter = $presenter;
    }

    /**
     * View all extension page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

}
