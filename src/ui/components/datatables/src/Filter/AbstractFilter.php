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

namespace Antares\Datatables\Filter;

use Antares\Asset\Factory as AssetFactory;
use Illuminate\Session\Store as Session;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Router;
use Antares\Html\HtmlBuilder;
use Antares\Asset\Asset;
use Exception;

abstract class AbstractFilter
{

    /**
     * base partial view path
     *
     * @var String 
     */
    protected $partialView = 'datatables-helpers::partials._filter';

    /**
     * assets instance
     *
     * @var Asset 
     */
    protected $container = null;

    /**
     * scripts container
     *
     * @var array 
     */
    protected $scripts = [];

    /**
     * filter pattern
     *
     * @var String
     */
    protected $pattern = '';

    /**
     *
     * @var Router
     */
    protected $router;

    /**
     *
     * @var HtmlBuilder
     */
    protected $htmlBuilder;

    /**
     * session container
     *
     * @var Session
     */
    protected $session;

    /**
     * form data container
     *
     * @var array
     */
    protected $data = [];

    /**
     * Whether filter is renderable
     *
     * @var boolean 
     */
    public $renderable = true;

    /**
     * constructing
     * 
     * @param AssetFactory $assetFactory
     */
    public function __construct()
    {
        $this->router      = Foundation::make(Router::class);
        $this->htmlBuilder = Foundation::make(HtmlBuilder::class);
        $this->session     = Foundation::make(Session::class);
        $this->container   = Foundation::make(AssetFactory::class)->container('antares/foundation::scripts');
    }

    /**
     * renders filter content
     */
    abstract public function render();

    /**
     * parameters getter
     * 
     * @param mixed $param
     * @return mixed
     * @throws Exception
     */
    public function get($param)
    {
        if (isset($this->{$param})) {
            return $this->{$param};
        }

        throw new Exception(sprintf('Filter variable "%s" not found.', $param));
    }

    /**
     * renders filter content
     * 
     * @return String
     */
    public function __toString()
    {
        try {
            $this->scripts();
            $input = $this->htmlBuilder->create('input', '', ['type' => 'hidden', 'value' => get_class($this), 'class' => 'classname'])->get();
            return '<li>' . $input . '</li>' . $this->render();
        } catch (Exception $ex) {
            Log::emergency($ex);
        }
    }

    /**
     * attaches filter scripts
     */
    protected function scripts()
    {
        if (empty($this->scripts)) {
            return false;
        }
        foreach ($this->scripts as $path) {
            $name = last(explode('/', $path));
            $this->container->add(str_slug($name), $path);
        }
    }

    /**
     * gets filter pattern
     * 
     * @return String
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * creates patterned sidebar title
     * 
     * @param mixed $value
     * @return String
     */
    public function getPatterned($value)
    {
        if (!is_array($value)) {
            return str_replace('%value', ucfirst($value), $this->pattern);
        } else {
            $return = $this->pattern;
            foreach ($value as $field) {
                if (!isset($field['name']) and ! isset($field['value'])) {
                    continue;
                }
                if (isset($field['value']) && is_array($field['value'])) {
                    $field['value'] = implode(',', $field['value']);
                }
                $return = str_replace('%' . $field['name'], $field['value'], $return);
            }
            return $return;
        }
    }

    /**
     * gets filter params from session
     * 
     * @return String
     */
    protected function getParams($key = null)
    {
        $params = $this->session->get(uri());
        return !is_null($key) ? array_get($params, $key) : $params;
    }

    /**
     * form data setter
     * 
     * @param array $data
     * @return \Antares\Datatables\Filter\AbstractFilter
     */
    public function setFormData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Gets sidebar html
     * 
     * @param array $data
     * @return string
     */
    public function sidebar(array $data = [])
    {
        return '';
    }

    /**
     * Column name getter
     * 
     * @return String
     */
    public function getColumn()
    {
        return $this->column;
    }

}
