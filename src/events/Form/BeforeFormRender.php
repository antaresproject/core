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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Form;

use Antares\Html\Form\Grid;
use Antares\Foundation\Events\AbstractEvent;

class BeforeFormRender extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Before form rendered';

    /** @var string */
    protected static $description = 'Runs before form is rendered';

    /** @var Grid */
    public $grid;

    /**
     * BeforeFormRender constructor
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        parent::__construct();
    }

}
