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

class ComponentInstallationFailed extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Components: Component installation failed';

    /** @var string */
    protected static $description = 'Runs after component intallation failed';

    /** @var ExtensionContract */
    public $component;

    /** @var \Exception */
    public $exception;

    /**
     * ComponentInstallationFailed constructor
     *
     * @param ExtensionContract $component
     * @param \Exception        $exception
     */
    public function __construct(ExtensionContract $component, \Exception $exception)
    {
        $this->component = $component;
        $this->exception = $exception;

        parent::__construct();
    }

}
