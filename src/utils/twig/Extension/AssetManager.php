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


namespace Antares\Twig\Extension;

use Twig_Extension;
use Twig_SimpleFunction;
use Illuminate\Support\Str;
use Antares\Support\Facades\Theme;
use Antares\Asset\Factory;

/**
 * Access Laravels asset class in your Twig templates.
 */
class AssetManager extends Twig_Extension
{

    /**
     * @var Antares\Asset\Factory 
     */
    protected $factory;

    /**
     * constructing
     * @param Factory $asset
     */
    public function __construct(Factory $asset)
    {
        $this->factory = $asset->container('antares.header');
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_AssetManager';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $assetm = new Twig_SimpleFunction(
                'assetm', function ($name) {
            $arguments = array_slice(func_get_args(), 1);
            $name      = Str::camel($name);
            return call_user_func_array([$this, $name], $arguments);
        });

        $assetshow = new Twig_SimpleFunction(
                'assetm_*', function ($name) {
            $arguments = array_slice(func_get_args(), 1);
            $name      = Str::camel($name);
            echo call_user_func_array([$this->factory, $name], $arguments);
        });

        return [
            $assetm, $assetshow
        ];
    }

    /**
     * @see Factory::style
     * @param String $name
     * @param String $path
     * @param array $dependencies
     */
    public function style($name = null, $path = null, array $dependencies = [])
    {
        $assetPath = $this->resolvePath($path);
        $this->factory->style($name, $assetPath, $dependencies);
    }

    /**
     * @see Factory::script
     * @param String $name
     * @param String $path
     * @param array $dependencies
     */
    public function script($name = null, $path = null, array $dependencies = [])
    {
        $assetPath = $this->resolvePath($path);
        $this->factory->script($name, $assetPath, $dependencies);
    }

    /**
     * path resolver
     * @param String $path
     * @return String
     */
    protected function resolvePath($path)
    {
        return starts_with($path, 'theme::') ? Theme::asset(str_replace('theme::', '', $path)) : $path;
    }

}
