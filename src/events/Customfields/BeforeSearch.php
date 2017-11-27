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

namespace Antares\Events\Customfields;

use Antares\Foundation\Events\AbstractEvent;

class BeforeSearch extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Custom Fields: Before search';

    /** @var string */
    protected static $description = 'Runs before custom fields search is being executed (????)';

    /** @var mixed */
    public $return;

    /**
     * BeforeSearch constructor
     *
     * @param mixed $return
     */
    public function __construct($return)
    {
        $this->return = $return;

        parent::__construct();
    }

}
