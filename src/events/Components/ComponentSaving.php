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

class ComponentSaving extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component settings saving';

    /** @var string */
    protected static $description = 'Runs before saving component settings';

    /** @var string */
    public $componentName;

    /** @var array */
    public $data;

    /**
     * ComponentBooted constructor
     *
     * @param string $componentName
     * @param array  $data
     */
    public function __construct(string $componentName, array &$data = [])
    {
        $this->componentName = $componentName;
        $this->data = $data;

        parent::__construct();
    }

}
