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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\Twig;

use Antares\Brands\Facade\StylerFacade;
use Twig_SimpleFunction;
use Twig_Extension;

class Brand extends Twig_Extension
{

    /**
     * logos directory
     *
     * @var String 
     */
    protected static $logoPath = '/_dist/img/theme/antares/logo/';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'TwigBridge_Logger_Extension_Brand';
    }

    /**
     * registry getter
     * 
     * @return \Antares\Memory\Handler
     */
    protected function registry()
    {

        return app('antares.memory')->make('registry');
    }

    /**
     * create widget view helper for get brand logo
     * 
     * @return Twig_SimpleFunction
     */
    protected function brandLogo()
    {
        $function = function ($param = 'logo', $default = null) {
            return brand_logo($param, $default);
        };
        return new Twig_SimpleFunction(
                'brand_logo', $function
        );
    }

    /**
     * create widget view helper for get brand title
     * 
     * @return Twig_SimpleFunction
     */
    protected function brandTitle()
    {

        $function = function () {
            $title = $this->registry()->get('brand.configuration.name');
            return strlen($title) <= 0 ? app('antares.memory')->make('primary')->get('site.name') : $title;
        };
        return new Twig_SimpleFunction(
                'brand_title', $function
        );
    }

    /**
     * create widget view helper for brand styles
     * 
     * @return Twig_SimpleFunction
     */
    protected function brandStyles()
    {

        $function = function () {
            $colors = $this->registry()->get('brand.configuration.template.colors', []);

//            if (empty($colors)) {
//                return '';
//            }
            return StylerFacade::layoutAdapter($colors)->style();
        };
        return new Twig_SimpleFunction(
                'brand_styles', $function
        );
    }

    /**
     * Gets information about brand composition
     * 
     * @return Twig_SimpleFunction
     */
    protected function brandComposition()
    {
        $function = function ($name = null) {
            $composition = $this->registry()->get('brand.configuration.template.composition');
            return !is_null($name) ? $composition == $name : $composition;
        };
        return new Twig_SimpleFunction(
                'brand_composition', $function
        );
    }

    /**
     * Gets information about brand styleset
     * 
     * @return Twig_SimpleFunction
     */
    protected function brandStyleset()
    {

        $function = function ($name = null) {
            $styleset = $this->registry()->get('brand.configuration.template.styleset');
            return !is_null($name) ? $styleset == $name : $styleset;
        };
        return new Twig_SimpleFunction(
                'brand_styleset', $function
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            $this->brandLogo(), $this->brandTitle(), $this->brandStyles(), $this->brandComposition(), $this->brandStyleset()
        ];
    }

}
