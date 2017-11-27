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

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;

class TopCenterContent extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Top center content';

    /** @var string */
    protected static $description = 'Runs when Datatable is rendered, allows to change content of table (search field etc.)';

    /** @var string */
    public $datatableName;

    /** @var string */
    public $content;

    /**
     * TopCenterContent constructor
     *
     * @param string $datatableName
     * @param string $content
     */
    public function __construct(string $datatableName, string &$content)
    {
        $this->datatableName = $datatableName;
        $this->content = $content;

        parent::__construct();
    }

}
