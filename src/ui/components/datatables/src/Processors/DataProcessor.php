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
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Datatables\Processors;

use Yajra\Datatables\Processors\DataProcessor as SupportDataProcessor;

class DataProcessor extends SupportDataProcessor
{

    /**
     * @param mixed $results
     * @param array $columnDef
     * @param array $templates
     * @param int $start
     */
    public function __construct($results, array $columnDef, array $templates, $start = null)
    {
        $this->results       = $results;
        $this->appendColumns = $columnDef['append'];
        $this->editColumns   = $columnDef['edit'];
        $this->excessColumns = $columnDef['excess'];
        $this->escapeColumns = $columnDef['escape'];
        $this->templates     = $templates;
        $this->start         = $start;
    }

}
