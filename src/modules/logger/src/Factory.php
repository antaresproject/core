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



namespace Antares\Logger;

use Antares\Logger\Contracts\Factory as FactoryContract;
use Illuminate\Contracts\Foundation\Application;
use Antares\Logger\Adapter\LoginDeviceAdapter;
use Illuminate\Support\Facades\Request;
use Antares\Logger\Model\LogPriorities;
use Antares\Logger\Model\LogTypes;
use Illuminate\Support\Facades\DB;
use Antares\Logger\Model\Logs;
use Antares\GeoIP\GeoIPFacade;
use ReflectionClass;
use Exception;
use DateTime;

class Factory implements FactoryContract
{

    /**
     * ignore ajax requests flag
     *
     * @var boolean 
     */
    protected static $ignoreAjax = true;

    /**
     * application instance
     *
     * @var Application
     */
    protected $app;

    /**
     * login device adapter instance
     *
     * @var LoginDeviceAdapter 
     */
    protected $loginDeviceAdapter;

    /**
     * Old data for keep 
     *
     * @var array
     */
    protected $old = [];

    /**
     * Owner instance
     *
     * @var mixed
     */
    protected $owner = null;

    /**
     * Additional object params
     *
     * @var array
     */
    protected $additionalParams = [];

    /**
     * Related data
     *
     * @var array
     */
    protected $relatedData = [];

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app                = $app;
        $this->loginDeviceAdapter = $app->make(LoginDeviceAdapter::class);
    }

    /**
     * Creates name of log entity
     * 
     * @param String $classname
     * @param String $function
     * @return String
     */
    protected function getName($classname, $function = null)
    {
        $name = str_replace('Controller', '', last(explode('\\', $classname)));
        return strtoupper(!is_null($function) ? $name . '_' . $function : $name);
    }

    /**
     * validation before save log data
     * 
     * @return type
     */
    protected function validate()
    {
        return !app('request')->ajax();
    }

    /**
     * Owner instance setter
     * 
     * @param mixed $owner
     * @return \Antares\Logger\Factory
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Params setter
     * 
     * @param array $params
     * @return \Antares\Logger\Factory
     */
    public function setAdditionalParams(array $params = [])
    {
        $this->additionalParams = $params;
        return $this;
    }

    /**
     * insert customized log data 
     * 
     * @param Strong $priority
     * @return boolean
     */
    public function keep($priority = null)
    {
        if (!$this->validate()) {
            return;
        }
        $insert   = array_merge($this->prepare(), $this->getObjectParams($priority));
        $logs     = new Logs($insert);
        $logs->save();
        $segments = request()->segments();
        if (end($segments) == 'login') {
            try {
                $location = $this->app->make('geoip')->getLocation();
            } catch (Exception $ex) {
                $location = 'not reached';
            }
            $this->loginDeviceAdapter->validate(array_merge($insert, ['log_id' => $logs->id, 'location' => $location]));
        }

        return true;
    }

    public function getLoggerParams($priority, $params = [])
    {
        return array_merge($this->prepare(), $this->getObjectParams($priority, $params));
    }

    /**
     * Gets object params
     * 
     * @param String $priority
     * @return array
     */
    protected function getObjectParams($priority, array $params = [])
    {
        if (!empty($params)) {
            $object = array_get($params, 'object');
            $name   = array_get($params, 'name');
        } elseif (!is_null($this->owner)) {
            $object = $this->owner;
            $name   = $this->getName($owner);
        } else {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2];
            $object    = array_get($backtrace, 'object');
            $name      = $this->getName(get_class($object), array_get($backtrace, 'function'));
        }
        if (!is_object($object) or is_null($name)) {
            throw new Exception('Unable to resolve logger object params');
        }

        return [
            'type_id'     => $this->logTypeId($object),
            'owner_type'  => get_class($object),
            'old_value'   => $this->old,
            'priority_id' => $this->logPrioriotyId($priority),
            'name'        => $name
        ];
    }

    /**
     * Related data setter
     * 
     * @param array $relatedData
     * @return \Antares\Logger\Factory
     */
    public function setRelatedData($relatedData)
    {
        $this->relatedData = $relatedData;
        return $this;
    }

    /**
     * Sets old data 
     * 
     * @param array $old
     * @return \Antares\Logger\Factory
     */
    public function setOld(array $old = [])
    {
        $this->old = $old;
        return $this;
    }

    /**
     * traverse all user actions
     * 
     * @return boolean
     */
    public function traverse()
    {

        if (self::$ignoreAjax or ! $this->validate()) {
            return;
        }
        $request    = app('antares.request');
        $module     = $request->getModule();
        $controller = $request->getController();
        $action     = $request->getAction();
        $insert     = [
            'type_id'     => $this->getLogTypeByName($module),
            'owner_type'  => $request->getControllerClass(),
            'priority_id' => $this->logPrioriotyId('low'),
            'name'        => strtoupper(implode('_', [$module, $controller, $action])),
            'type'        => 'viewed'
        ];

        return Logs::insert(array_merge($this->prepare(), $insert));
    }

    /**
     * prepare insert log data
     * 
     * @return array
     */
    protected function prepare()
    {
        return [
            'route'             => str_replace(app('url')->to('/'), '', Request::url()),
            'ip_address'        => GeoIPFacade::getClientIP(),
            'user_agent'        => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No UserAgent',
            'created_at'        => new DateTime(),
            'updated_at'        => new DateTime(),
            'type'              => 'dispatched',
            'user_id'           => auth()->guest() ? null : auth()->user()->id,
            'brand_id'          => brand_id(),
            'additional_params' => $this->additionalParams,
            'related_data'      => $this->relatedData
        ];
    }

    /**
     * get instance of log priority
     * 
     * @param String $priority
     * @return LogPriorities
     */
    public function logPrioriotyId($priority = 'medium')
    {
        $logPriority = LogPriorities::where('name', $priority)->first();
        if (is_null($logPriority)) {
            $logPriority = new LogPriorities(['name' => $priority]);
            $logPriority->save();
        }
        return $logPriority->id;
    }

    /**
     * get instance of log type
     * 
     * @param mixed $object
     * @return numeric
     */
    public function logTypeId($object)
    {
        $reflection = new ReflectionClass($object);
        $filename   = $reflection->getFileName();

        $match = null;

        if (!preg_match("'src(.*?)src'si", $filename, $match)) {
            throw new Exception('Unable to resolve current module name.');
        }
        if (!isset($match[1])) {
            throw new Exception('Unable to resolve current module namespace.');
        }

        $reserved = [
            'components', 'modules'
        ];
        $name     = (str_contains($match[1], 'core')) ? 'core' : trim(str_replace($reserved, '', $match[1]), DIRECTORY_SEPARATOR);
        return $this->getLogTypeByName($name);
    }

    /**
     * get log type by name
     * 
     * @param String $name
     * @return numeric
     */
    protected function getLogTypeByName($name)
    {
        $type = LogTypes::where('name', $name)->first();
        if (is_null($type)) {
            $type = new LogTypes(['name' => $name]);
            $type->save();
        }
        return $type->id;
    }

    /**
     * get default notification sender adapter
     * 
     * @return mixed
     */
    public function getAdapter()
    {
        $className = $this->app->make('config')->get('antares/logger::adapter.default.model');
        return $this->app->make($className);
    }

    /**
     * get instance of log viewer
     * 
     * @return Utilities\LogViewer
     */
    public function logViewer()
    {
        return $this->app->make('Antares\Logger\Utilities\LogViewer');
    }

    /**
     * Main model getter
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getMainModel()
    {
        return $this->app->make(Logs::class);
    }

}
