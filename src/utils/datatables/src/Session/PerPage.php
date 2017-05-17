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
        if (php_sapi_name() === 'cli') {
            return 10;
        }

        if (!$this->request->hasSession()) {
            return;
        }
        $key     = $this->getSessionKey(get_class($datatable));
        $perPage = $this->getFromSession($key, (int) request('length', $datatable->perPage));
        return is_null($perPage) ? 10 : $perPage;
    }

}
