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

namespace Antares\Extension;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Antares\Contracts\Extension\RouteGenerator as RouteGeneratorContract;

class RouteGenerator implements RouteGeneratorContract
{

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * Domain name.
     *
     * @var string
     */
    protected $domain;

    /**
     * Handles path.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Base URL.
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * Base URL prefix.
     *
     * @var string
     */
    protected $basePrefix = null;

    /**
     * Construct a new instance.
     *
     * @param  string  $handles
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct($handles = null, Request $request)
    {

        $this->request = $request;

        $this->setBaseUrl($this->request->root());

        if ($handles === null || ! Str::startsWith($handles, ['//', 'http://', 'https://'])) {
            $this->prefix = $handles;
        } else {
            $handles      = substr(str_replace(['http://', 'https://'], '//', $handles), 2);
            $fragments    = explode('/', $handles, 2);
            $this->domain = array_shift($fragments);
            $this->prefix = array_shift($fragments);
        }
        !is_null($this->prefix) || $this->prefix = '/';
    }

    /**
     * Get route domain.
     *
     * @param  bool  $forceBase
     *
     * @return string
     */
    public function domain($forceBase = false)
    {
        $pattern = $this->domain;

        if ($pattern === null && $forceBase === true) {
            $pattern = $this->baseUrl;
        } elseif (Str::contains($pattern, '{{domain}}')) {
            $pattern = str_replace('{{domain}}', $this->baseUrl, $pattern);
        }

        return $pattern;
    }

    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param  string  $pattern
     *
     * @return bool
     */
    public function is($pattern)
    {
        $path   = $this->path();
        $prefix = $this->prefix();

        foreach (func_get_args() as $_pattern) {
			$_pattern = ($_pattern === '*' ? "{$prefix}*" : "{$prefix}/{$_pattern}");
			$_pattern = trim($_pattern, '/');

            empty($_pattern) && $_pattern = '/';

            if (Str::is($_pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->request->path(), '/');

        return $pattern === '' ? '/' : $pattern;
    }

    /**
     * Get route prefix.
     *
     * @param  bool  $forceBase
     *
     * @return string
     */
    public function prefix($forceBase = false)
    {
        if (!is_string($this->prefix)) {
            return '/';
        }
        $pattern = trim($this->prefix, '/');

        if ($this->domain === null && $forceBase === true) {
            $pattern = trim($this->basePrefix, '/') . "/{$pattern}";
            $pattern = trim($pattern, '/');
        }
        empty($pattern) && $pattern = '/';
        return $pattern;
    }

    /**
     * Get route root.
     *
     * @return string
     */
    public function root()
    {
        $http   = ($this->request->secure() ? 'https' : 'http');
        $domain = trim($this->domain(true), '/');
        $prefix = $this->prefix(true);

        return trim("{$http}://{$domain}/{$prefix}", '/');
    }

    /**
     * Set base URL.
     *
     * @param  string  $root
     *
     * @return $this
     */
    public function setBaseUrl($root)
    {
        $baseUrl = str_replace(['https://', 'http://'], '', $root);
        $base    = explode('/', $baseUrl, 2);

        if (count($base) > 1) {
            $this->basePrefix = array_pop($base);
        }


        $this->baseUrl = array_shift($base);

        return $this;
    }

    /**
     * Get route to.
     *
     * @param  string  $to
     *
     * @return string
     */
    public function to($to)
    {
        $root    = $this->root();
        $to      = trim($to, '/');
        $pattern = trim("{$root}/{$to}", '/');

        return $pattern !== '/' ? $pattern : '';
    }

    /**
     * Magic method to parse as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->prefix();
    }

    /**
     * Member prefix setter
     *
     * @param string $prefix
     * @return RouteGenerator
     */
    public function setPrefix($prefix = null)
    {
        $this->prefix = ($prefix === null) ? 'member' : $prefix;

        return $this;
    }

    /**
     * Sets user level as prefix in route
     *
     * @return RouteGenerator
     */
    public function setAreaPrefix()
    {
        if (auth()->guest()) {
            return $this;
        }

        $this->prefix = app('antares.areas')->getUserArea();

        return $this;
    }

}
