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


namespace Antares\Twig\Extension\Laravel;

use Twig_Extension;
use Twig_SimpleFunction;
use Collective\Html\HtmlBuilder;
use Illuminate\Support\Str;

/**
 * Access Laravels html builder in your Twig templates.
 */
class Html extends Twig_Extension
{

    /**
     * @var \Collective\Html\HtmlBuilder
     */
    protected $html;

    /**
     * Create a new html extension
     *
     * @param \Collective\Html\HtmlBuilder
     */
    public function __construct()
    {
        $this->html = app(\Collective\Html\HtmlBuilder::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Extension_Laravel_Html';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('link_to', [$this->html, 'link'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_asset', [$this->html, 'linkAsset'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_route', [$this->html, 'linkRoute'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('link_to_action', [$this->html, 'linkAction'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction(
                    'html_*', function ($name) {
                $arguments = array_slice(func_get_args(), 1);
                $name      = Str::camel($name);

                return call_user_func_array([$this->html, $name], $arguments);
            }, [
                'is_safe' => ['html'],
                    ]
            ),
        ];
    }

}
