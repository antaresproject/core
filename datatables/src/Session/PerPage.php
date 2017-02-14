<?php

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
