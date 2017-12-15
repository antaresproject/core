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
use Antares\Datatables\Adapter\FilterAdapter;

class BeforeFilters extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: Before filter';

    /** @var string */
    protected static $description = 'Runs before filter is added';

    /** @var string */
    public $uri;

    /** @var string */
    public $filter;

    /** @var FilterAdapter */
    public $filterAdapter;

    /** @var */
    public $query;

    /**
     * BeforeFilters constructor
     *
     * @param string        $uri
     * @param string        $filter
     * @param FilterAdapter $filterAdapter
     * @param               $query
     */
    public function __construct($uri, $filter, FilterAdapter $filterAdapter, $query)
    {
        $this->uri           = $uri;
        $this->filter        = $filter;
        $this->filterAdapter = $filterAdapter;
        $this->query         = $query;

        parent::__construct();
    }

}
