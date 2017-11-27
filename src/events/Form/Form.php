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

use Antares\Foundation\Events\AbstractEvent;

class Form extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Form: created';

    /** @var string */
    protected static $description = 'Runs when form is created';

    /** @var string */
    public $formName;

    /**
     * Form constructor
     *
     * @param string $formName
     */
    public function __construct(string $formName)
    {
        $this->formName = $formName;

        parent::__construct();
    }

}
