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

namespace Antares\Events\Placeholder;

use Antares\Foundation\Events\AbstractEvent;

class Before extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Placeholder: Before';

    /** @var string */
    protected static $description = 'Runs before placeholder is rendered';

    /** @var string */
    public $placeholderName;

    /** @var  */
    public $values;

    /**
     * Before constructor
     *
     * @param string    $placeholderName
     * @param Blueprint $values
     */
    public function __construct(string $placeholderName, $values)
    {
        $this->placeholderName = $placeholderName;
        $this->values = $values;

        parent::__construct();
    }

}
