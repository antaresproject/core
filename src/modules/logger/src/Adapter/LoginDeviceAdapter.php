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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Adapter;

use Antares\Logger\Model\LogsLoginDevices;
use Illuminate\Events\Dispatcher;
use Exception;

class LoginDeviceAdapter
{

    /**
     * eloquent model instance
     *
     * @var \Illuminate\Database\Eloquent\Model 
     */
    protected $model;

    /**
     * event dispatcher instance
     *
     * @var Dispatcher 
     */
    protected $dispatcher;

    /**
     * constructing
     * 
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->model      = new LogsLoginDevices();
        $this->dispatcher = $dispatcher;
    }

    /**
     * validates and saves new login attempt
     * 
     * @param array $params
     * @return boolean
     */
    public function validate(array $params = [])
    {
        $name = array_get($params, 'name');
        if (is_null($name) or $name != config('antares/logger::operations.login')) {
            return false;
        }
        $params = array_merge($params, $this->getClientParams());
        try {
            $model = $this->model->newQuery()
                    ->where('user_id', $params['user_id'])
                    ->where('ip_address', $params['ip_address'])
                    ->where('browser', $params['browser'])
                    ->orderBy('created_at', 'desc')
                    ->firstOrFail();
            $model->touch();
        } catch (Exception $ex) {
            $this->saveNewDevice($params);
        }
        if (isset($model) and ! is_null($model) and ( $model->ip_address != $params['ip_address'] or $model->browser != $params['browser'])) {
            $this->saveNewDevice($params);
            $user = user();
            $this->dispatcher->fire('new-device-detect-notification', ['variables' => ['user' => $user, 'params' => $params, 'date' => date('Y-m-d', time()), 'time' => date("H:i", time())], 'recipients' => [$user]]);
        }
    }

    /**
     * saves when new device detected
     * 
     * @param array $params
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function saveNewDevice($params)
    {
        unset($params['name']);
        $this->model->fill($params);
        return $this->model->save();
    }

    /**
     * get client params
     * 
     * @return array
     */
    protected function getClientParams()
    {
        $agent    = $_SERVER['HTTP_USER_AGENT'];
        $bname    = 'Unknown';
        $platform = 'Unknown';

        if (preg_match('/linux/i', $agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $agent)) {
            $platform = 'Windows';
        }
        if (preg_match('/Edge/i', $agent) && !preg_match('/Opera/i', $agent)) {
            $bname = 'Internet Explorer';
            $ub    = "MSIE";
        } elseif (preg_match('/MSIE/i', $agent) && !preg_match('/Opera/i', $agent)) {
            $bname = 'Internet Explorer';
            $ub    = "MSIE";
        } elseif (preg_match('/Firefox/i', $agent)) {
            $bname = 'Mozilla Firefox';
            $ub    = "Firefox";
        } elseif (preg_match('/Chrome/i', $agent)) {
            $bname = 'Google Chrome';
            $ub    = "Chrome";
        } elseif (preg_match('/Safari/i', $agent)) {
            $bname = 'Apple Safari';
            $ub    = "Safari";
        } elseif (preg_match('/Opera/i', $agent)) {
            $bname = 'Opera';
            $ub    = "Opera";
        } elseif (preg_match('/Netscape/i', $agent)) {
            $bname = 'Netscape';
            $ub    = "Netscape";
        }
        return [
            'browser' => $bname,
            'system'  => $platform,
            'machine' => php_uname('n')
        ];
    }

}
