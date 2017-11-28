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

namespace Antares\Events\Compontents;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Extension\Contracts\ExtensionContract;

class ComponentActivated extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component activated';

    /** @var string */
    protected static $description = 'Runs after component activation';

    /** @var ExtensionContract */
    public $component;

    /**
     * ComponentActivated constructor
     *
     * @param ExtensionContract $component
     */
    public function __construct(ExtensionContract $component)
    {
        $this->component = $component;

        parent::__construct();
    }

}
