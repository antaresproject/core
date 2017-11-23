<?php

namespace Antares\Events\Datatables;

use Antares\Foundation\Events\AbstractEvent;
use Antares\Datatables\Adapter\FilterAdapter;

class AfterFilters extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Datatables: After filter';

    /** @var string */
    protected static $description = 'Runs after filter is added';

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
    public function __construct(string $uri, string $filter, FilterAdapter $filterAdapter, $query)
    {
        $this->uri = $uri;
        $this->filter = $filter;
        $this->filterAdapter = $filterAdapter;
        $this->query = $query;

        parent::__construct();
    }

}
