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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Adapter;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Log;
use Exception;

class JsonAdapter extends AbstractAdapter
{

    /**
     * retrive information about system version from external service
     * 
     * @return mixed
     */
    public function retrive()
    {
        $this->data = $this->checkout();
        $path       = isset($this->data['modules']) ? $this->data['modules'] : null;
        if (!is_null($path)) {
            $this->data['modules'] = $this->checkout($path);
        }
        return $this->data;
    }

    /**
     * retrive module details for update
     * 
     * @param String $name
     * @param String $version
     * @return array
     */
    public function retriveModule($name, $version)
    {
        $retrived = $this->retrive();
        $modules  = $retrived['modules'];
        $return   = null;
        foreach ($modules as $module) {
            $moduleName    = array_get($module, 'name');
            $moduleVersion = array_get($module, 'version');
            if ($moduleName == $name && $moduleVersion == $version) {
                $return = $module;
                break;
            }
        }
        return $return;
    }

    /**
     * checkout information about new system version
     * 
     * @param String $path
     * @return boolean
     * @throws NotFoundHttpException
     */
    protected function checkout($path = null)
    {
        try {
            if (!extension_loaded('curl')) {
                return false;
            }
            $url = !is_null($path) ? $path : array_get($this->config, 'path');
            if (is_null($url)) {
                throw new NotFoundHttpException('Invalid path of system version service.');
            }
            $ch     = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);
            if (strlen($result) > 0) {
                return json_decode($result, true);
            }
        } catch (Exception $e) {
            Log::emergency($e);
            return false;
        }
    }

}
