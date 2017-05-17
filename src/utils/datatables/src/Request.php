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

namespace Antares\Datatables;

use Yajra\Datatables\Request as YajraRequest;

/**
 * Class Request.
 *
 * @property array columns
 * @package Yajra\Datatables
 * @author  Arjay Angeles <aqangeles@gmail.com>
 */
class Request extends YajraRequest
{

    /**
     * Get searchable column indexes
     *
     * @return array
     */
    public function searchableColumnIndex()
    {
        $searchable = [];
        for ($i = 0, $c = count($this->get('columns')); $i < $c; $i++) {
            if ($this->isColumnSearchable($i, false)) {
                $searchable[] = $i;
            }
        }

        return $searchable;
    }

    public function setSearchableColumnIndex($columns, $filters)
    {

        if ($this->has('columns')) {
            return $this;
        }
        $replace = [];
        foreach ($columns as $column) {
            $replace[] = [
                "data"       => $column,
                "name"       => $column,
                "searchable" => true,
                "orderable"  => false,
                "search"     => [
                    'value' => ''
                ],
            ];
        }
        $this->merge([
            'columns' => $replace
        ]);
    }

    /**
     * Check if Datatables is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        if (request()->has('inline_search')) {
            $this->replace([
                'start'  => 0,
                'length' => 25,
                'search' => request()->get('inline_search')
            ]);
        }
        $search = (array) $this->get('search');
        return isset($search['value']) ? $search['value'] != '' : false;
    }

}
