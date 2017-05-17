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
 namespace Antares\Html\Support\Traits;

use Illuminate\Support\Arr;

trait CreatorTrait
{
    /**
     * The current model instance for the form.
     *
     * @var mixed
     */
    protected $model;

    /**
     * An array of label names we've created.
     *
     * @var array
     */
    protected $labels = [];

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * The reserved form open attributes.
     *
     * @var array
     */
    protected $reserved = ['method', 'url', 'route', 'action', 'files'];

    /**
     * The form methods that should be spoofed, in uppercase.
     *
     * @var array
     */
    protected $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

    /**
     * Open up a new HTML form.
     *
     * @param  array   $options
     *
     * @return string
     */
    public function open(array $options = [])
    {
        $method = Arr::get($options, 'method', 'post');

                                $attributes = [
            'method'         => $this->getMethod($method),
            'action'         => $this->getAction($options),
            'accept-charset' => 'UTF-8',
        ];

                                $append = $this->getAppendage($method);

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

                                $attributes = array_merge($attributes, Arr::except($options, $this->reserved));

                                $attributes = $this->getHtmlBuilder()->attributes($attributes);

        return '<form'.$attributes.'>'.$append;
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        $this->labels = [];

        $this->model = null;

        return '</form>';
    }

    /**
     * Get the form appendage for the given method.
     *
     * @param  string  $method
     *
     * @return string
     */
    protected function getAppendage($method)
    {
        list($method, $appendage) = [strtoupper($method), ''];

                                if (in_array($method, $this->spoofedMethods)) {
            $appendage .= $this->hidden('_method', $method);
        }

                                if ($method != 'GET') {
            $appendage .= $this->token();
        }

        return $appendage;
    }

    /**
     * Parse the form action method.
     *
     * @param  string  $method
     *
     * @return string
     */
    protected function getMethod($method)
    {
        $method = strtoupper($method);

        return $method != 'GET' ? 'POST' : $method;
    }
    /**
     * Get the form action from the options.
     *
     * @param  array   $options
     *
     * @return string
     */
    protected function getAction(array $options)
    {
                                if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

                        
        if (isset($options['route'])) {
            return $this->getRouteAction($options['route']);
        } elseif (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->url->current();
    }

    /**
     * Get the action for a "url" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getUrlAction($options)
    {
        if (is_array($options)) {
            return $this->url->to($options[0], array_slice($options, 1));
        }

        return $this->url->to($options);
    }

    /**
     * Get the action for a "route" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getRouteAction($options)
    {
        if (is_array($options)) {
            return $this->url->route($options[0], array_slice($options, 1));
        }

        return $this->url->route($options);
    }

    /**
     * Get the action for an "action" option.
     *
     * @param  array|string  $options
     *
     * @return string
     */
    protected function getControllerAction($options)
    {
        if (is_array($options)) {
            return $this->url->action($options[0], array_slice($options, 1));
        }

        return $this->url->action($options);
    }

    /**
     * Generate a hidden field with the current CSRF token.
     *
     * @return string
     */
    abstract public function token();

    /**
     * Get html builder.
     *
     * @return \Antares\Html\Support\HtmlBuilder
     */
    abstract public function getHtmlBuilder();
}
