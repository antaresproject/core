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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Foundation\Processor;

use Antares\Datatables\Adapter\FilterAdapter;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use function array_get;

class FilterProcessor
{

    /**
     * request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * filter adapter instance
     *
     * @var FilterAdapter 
     */
    protected $adapter;

    /**
     * constructing
     * 
     * @param Request $request
     * @param FilterAdapter $adapter
     */
    public function __construct(Request $request, FilterAdapter $adapter)
    {
        $this->request = $request;
        $this->adapter = $adapter;
    }

    /**
     * stores filter data in session
     * 
     * @return type
     */
    public function store()
    {
        $inputs    = Input::all();
        $params    = array_get($inputs, 'params');
        $column    = $params['column'];
        $route     = uri();
        $session   = $this->request->session();
        $classname = array_get($inputs, 'classname');
        $data      = $this->prepareToSave($classname, $params, $session->get($route));

        $session->put($route, $data);
        $session->save();
        $data   = $this->prepareToSave($classname, $params, $session->get($route));
        $string = app($classname)->sidebar($data);
        if (strlen($string) > 0) {
            return response($string, 200);
        }

        $sidebar = $this->adapter->sidebar($column, $data[$column], $params['value']);
        return response($sidebar, 200);
    }

    /**
     * prepares data to store in session
     * 
     * @param String $classname
     * @param array $params
     * @param array $paramsFromSession
     * @return array
     */
    protected function prepareToSave($classname, array $params = [], array $paramsFromSession = null)
    {
        $column = $params['column'];
        $return = [];
        if (is_null($paramsFromSession)) {
            $return[$column]['classname'] = $classname;
            $return[$column]['values']    = [$params['value']];
            //this chunk of code will automatically add custom $params values
            //everything else then 'value' will be saved in session
            //this allow developer to send custom parameters through request
            foreach ($params as $key => $value) {
                if ($key == 'value') {
                    continue;
                }
                $return[$column][$key . 's'] = [$params[$key]];
            }
        } elseif (isset($paramsFromSession[$column])) {
            $paramsFromSession[$column]['values'][] = $params['value'];
            //this chunk of code will automatically add custom $params values
            //everything else then 'value' will be add to already existing key in session and saved
            //this allow developer to send custom parameters through request
            foreach ($params as $key => $value) {
                if ($key == 'value') {
                    continue;
                }
                $paramsFromSession[$column][$key . 's'][] = $params[$key];
                $return[$column][$key . 's']              = $paramsFromSession[$column][$key . 's'];
            }

            $return[$column]['values']    = $paramsFromSession[$column]['values'];
            $return[$column]['classname'] = $classname;
        } else {
            $return                       = $paramsFromSession;
            $return[$column]['classname'] = $classname;
            $return[$column]['values']    = [$params['value']];

            //this chunk of code will automatically add custom $params values
            //everything else then 'value' will be saved in session
            //this allow developer to send custom parameters through request 
            foreach ($params as $key => $value) {
                if ($key == 'value') {
                    continue;
                }
                $return[$column][$key . 's'] = [$params[$key]];
            }
        }
        if (!empty($paramsFromSession)) {
            foreach ($paramsFromSession as $storedColumn => $config) {
                if ($storedColumn == $column) {
                    continue;
                }
                $return[$storedColumn] = $config;
            }
        }
        return $return;
    }

    /**
     * deletes filter settings from session
     * 
     * @return type
     */
    public function destroy()
    {
        $inputs       = Input::all();
        $params       = array_get($inputs, 'params');
        if (($unserialized = $this->unserialize($params['value'])) !== false) {
            $params['value'] = $unserialized;
        }
        $route   = uri();
        $session = $this->request->session();
        if (!$session->has($route)) {
            return response('', 200);
        }
        $paramsFromSession = $session->get($route);
        $data              = $this->prepareToDestroy($params, $paramsFromSession);

        $session->put($route, $data);
        $session->save();
        return response('', 200);
    }

    /**
     * prepare data to remove from session
     * 
     * @param array $params
     * @param array $paramsFromSession
     * @return array
     */
    protected function prepareToDestroy(array $params = [], array $paramsFromSession = null)
    {
        $return = [];
        foreach ($paramsFromSession as $name => $values) {
            if ($params['column'] == $name) {
                $return[$name] = [];
            }
//            if (!empty($values['values'])) {
//                $return[$name]['values'] = $values['values'];
//            }
//            if (!isset($return[$name]['classname'])) {
//                $return[$name]['classname'] = $paramsFromSession[$name]['classname'];
//            }
        }
        return $return;
    }

    /**
     * custom string unserializator
     * 
     * @param String $str
     * @return boolean
     */
    protected function unserialize($str)
    {
        $str  = str_replace("'", '"', $str);
        $data = @unserialize($str);
        if ($str === 'b:0;' || $data !== false) {
            return $data;
        }
        return false;
    }

    /**
     * updates filter paramateres in session
     */
    public function update()
    {
        $inputs = Input::all();
        $s      = $this->request->session();
        $route  = uri();

        $session = $s->get($route, []);
        if (empty($session)) {
            return false;
        }
        if (is_null($column = array_get($inputs, 'column'))) {
            return false;
        }
        if (!isset($session[$column]['values'])) {
            return false;
        }

        $old        = !is_null($serialized = array_get($inputs, 'serialized')) ? unserialize($serialized) : array_get($inputs, 'old');
        $new        = array_get($inputs, 'new');
        $index      = array_search($old, $session[$column]['values']);
        if ($index !== false) {
            unset($session[$column]['values'][$index]);
        }
        $session[$column]['values'][] = $new;

        $sidebars = $this->adapter->getDeleteSidebarItem($column, $session[$column], $new);
        if (!empty($sidebars)) {
            $s->put($route, [$column => $session[$column]]);
            $s->save();
            return response(implode('', $sidebars), 200);
        }
    }

    /**
     * Additional save filter params
     * 
     * @return \Illuminate\Http\Response
     */
    public function save()
    {
        $input              = Input::all();
        $session            = app('request')->session();
        $classname          = array_get($input, 'classname');
        $key                = uri();
        $params             = $session->get($key);
        $params[$classname] = array_get($input, 'params');
        $session->put($key, $params);
        $session->save();
        return response(app($classname)->sidebar($params[$classname]), 200);
    }

    /**
     * Additional delete filter params
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        $column  = Input::get('params.column');
        $session = app('request')->session();
        $key     = uri();
        $params  = $session->get($key);
        $data    = [];
        foreach ($params as $name => $values) {
            if (isset($values['column']) && $values['column'] == $column) {
                continue;
            }
            $data[$name] = $values;
        }
        $session->put($key, $data);
        $session->save();
        return response('', 200);
    }

}
