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

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;

class FormReady extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form: ready';

    /** @var string */
    protected static $description = 'Runs when form is ready';

    /** @var string */
    public $formBuilder;

    /**
     * FormReady constructor
     *
     * @param FormBuilder $formBuilder
     */
    public function __construct(FormBuilder $formBuilder)
    {
        $this->formBuilder = $formBuilder;

        parent::__construct();
    }

}
