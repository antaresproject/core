<?php

/**
 * Part of the Antares Project package.
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
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Datatables\Session;

use Antares\Datatables\Services\DataTable;

class PerPage extends Session
{

    /**
     * Session perpage getter
     * 
     * @param DataTable $datatable
     * @return mixed
     */
    public function get(DataTable $datatable)
    {
        if (!$this->request->hasSession()) {
            return;
        }
        $key = $this->getSessionKey(get_class($datatable));
        return $this->getFromSession($key, (int) request('length', $datatable->perPage));
    }

}
