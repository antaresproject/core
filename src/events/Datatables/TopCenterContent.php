<?php

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
