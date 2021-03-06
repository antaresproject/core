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

namespace Antares\Html\Support;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Traits\Macroable;
use Antares\Html\Support\Traits\ObfuscateTrait;

class HtmlBuilder
{

    use Macroable,
        ObfuscateTrait;

    /**
     * The URL generator instance.
     *
     * @var \Illuminate\Routing\UrlGenerator
     */
    protected $url;

    /**
     * Create a new HTML builder instance.
     *
     * @param  \Illuminate\Routing\UrlGenerator  $url
     */
    public function __construct(UrlGenerator $url = null)
    {
        $this->url = $url;
    }

    /**
     * Convert an HTML string to entities.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function entities($value)
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Convert entities to HTML characters.
     *
     * @param  string  $value
     *
     * @return string
     */
    public function decode($value)
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate a meta tag.
     *
     * @param  string  $name
     * @param  string  $content
     * @param  array   $attributes
     *
     * @return string
     */
    public function meta($name, $content, array $attributes = [])
    {
        $defaults   = compact('name', 'content');
        $attributes = array_merge($defaults, $attributes);

        return '<meta' . $this->attributes($attributes) . '>' . PHP_EOL;
    }

    /**
     * Generate a link to a JavaScript file.
     *
     * @param  string  $url
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function script($url, $attributes = [], $secure = null)
    {
        $attributes['src'] = $this->url->asset($url, $secure);
        return '<script' . $this->attributes($attributes) . '></script>' . PHP_EOL;
    }

    /**
     * Generate an inline to a JavaScript code.
     *
     * @param  string  $source
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function inline($source, $attributes = [], $secure = null)
    {

        return '<script type="text/javascript" ' . $this->attributes($attributes) . '>' . $source . '</script>' . PHP_EOL;
    }

    /**
     * Generate a link to a CSS file.
     *
     * @param  string  $url
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function style($url, $attributes = [], $secure = null)
    {
        $defaults = ['media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet'];

        $attributes = $attributes + $defaults;

        $attributes['href'] = $this->url->asset($url, $secure);

        return '<link' . $this->attributes($attributes) . '>' . PHP_EOL;
    }

    /**
     * Generate an HTML image element.
     *
     * @param  string  $url
     * @param  string  $alt
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function image($url, $alt = null, $attributes = [], $secure = null)
    {
        $attributes['alt'] = $alt;

        return '<img src="' . $this->url->asset($url, $secure) . '"' . $this->attributes($attributes) . '>';
    }

    /**
     * Generate a HTML link.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function link($url, $title = null, $attributes = [], $secure = null)
    {
        $url = $this->url->to($url, [], $secure);

        if (is_null($title) || $title === false) {
            $title = $url;
        }

        return '<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $title . '</a>';
    }

    /**
     * Generate a HTTPS HTML link.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array   $attributes
     *
     * @return string
     */
    public function secureLink($url, $title = null, $attributes = [])
    {
        return $this->link($url, $title, $attributes, true);
    }

    /**
     * Generate a HTML link to an asset.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array   $attributes
     * @param  bool    $secure
     *
     * @return string
     */
    public function linkAsset($url, $title = null, $attributes = [], $secure = null)
    {
        $url = $this->url->asset($url, $secure);

        return $this->link($url, $title ?: $url, $attributes, $secure);
    }

    /**
     * Generate a HTTPS HTML link to an asset.
     *
     * @param  string  $url
     * @param  string  $title
     * @param  array   $attributes
     *
     * @return string
     */
    public function linkSecureAsset($url, $title = null, $attributes = [])
    {
        return $this->linkAsset($url, $title, $attributes, true);
    }

    /**
     * Generate a HTML link to a named route.
     *
     * @param  string  $name
     * @param  string  $title
     * @param  array   $parameters
     * @param  array   $attributes
     *
     * @return string
     */
    public function linkRoute($name, $title = null, $parameters = [], $attributes = [])
    {
        return $this->link($this->url->route($name, $parameters), $title, $attributes);
    }

    /**
     * Generate a HTML link to a controller action.
     *
     * @param  string  $action
     * @param  string  $title
     * @param  array   $parameters
     * @param  array   $attributes
     *
     * @return string
     */
    public function linkAction($action, $title = null, $parameters = [], $attributes = [])
    {
        return $this->link($this->url->action($action, $parameters), $title, $attributes);
    }

    /**
     * Generate a HTML link to an email address.
     *
     * @param  string  $email
     * @param  string  $title
     * @param  array   $attributes
     *
     * @return string
     */
    public function mailto($email, $title = null, $attributes = [])
    {
        $email = $this->email($email);

        $title = $title ?: $email;

        $email = $this->obfuscate('mailto:') . $email;

        return '<a href="' . $email . '"' . $this->attributes($attributes) . '>' . $this->entities($title) . '</a>';
    }

    /**
     * Obfuscate an e-mail address to prevent spam-bots from sniffing it.
     *
     * @param  string  $email
     *
     * @return string
     */
    public function email($email)
    {
        return str_replace('@', '&#64;', $this->obfuscate($email));
    }

    /**
     * Generate an ordered list of items.
     *
     * @param  array   $list
     * @param  array   $attributes
     *
     * @return string
     */
    public function ol($list, $attributes = [])
    {
        return $this->listing('ol', $list, $attributes);
    }

    /**
     * Generate an un-ordered list of items.
     *
     * @param  array   $list
     * @param  array   $attributes
     *
     * @return string
     */
    public function ul($list, $attributes = [])
    {
        return $this->listing('ul', $list, $attributes);
    }

    /**
     * Generate a description list of items.
     *
     * @param  array   $list
     * @param  array   $attributes
     *
     * @return string
     */
    public function dl(array $list, array $attributes = [])
    {
        $attributes = $this->attributes($attributes);
        $html       = "<dl{$attributes}>";

        foreach ($list as $key => $value) {
            $html .= "<dt>$key</dt><dd>$value</dd>";
        }

        $html .= '</dl>';

        return $html;
    }

    /**
     * Create a listing HTML element.
     *
     * @param  string  $type
     * @param  array   $list
     * @param  array   $attributes
     *
     * @return string
     */
    protected function listing($type, $list, $attributes = [])
    {
        $html = '';

        if (count($list) == 0) {
            return $html;
        }

        foreach ($list as $key => $value) {
            $html .= $this->listingElement($key, $type, $value);
        }

        $attributes = $this->attributes($attributes);

        return "<{$type}{$attributes}>{$html}</{$type}>";
    }

    /**
     * Create the HTML for a listing element.
     *
     * @param  mixed    $key
     * @param  string  $type
     * @param  string  $value
     *
     * @return string
     */
    protected function listingElement($key, $type, $value)
    {
        if (is_array($value)) {
            return $this->nestedListing($key, $type, $value);
        } else {
            return '<li>' . e($value) . '</li>';
        }
    }

    /**
     * Create the HTML for a nested listing attribute.
     *
     * @param  mixed   $key
     * @param  string  $type
     * @param  string  $value
     *
     * @return string
     */
    protected function nestedListing($key, $type, $value)
    {
        if (is_int($key)) {
            return $this->listing($type, $value);
        } else {
            return '<li>' . $key . $this->listing($type, $value) . '</li>';
        }
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     *
     * @return string
     */
    public function attributes($attributes)
    {
        $html = [];

        foreach ((array) $attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            !is_null($element) && $html[] = $element;
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string  $key
     * @param  string  $value
     *
     * @return string
     */
    protected function attributeElement($key, $value)
    {
        is_numeric($key) && $key = $value;

        if (is_array($value)) {
            $value = current($value);
        }

        if (!is_null($value)) {
            return $key . '="' . e($value) . '"';
        }
    }

}
