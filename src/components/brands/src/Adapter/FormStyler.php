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


namespace Antares\Brands\Adapter;

use Antares\Brands\Contracts\StylerAdapter;

class FormStyler extends AbstractAdapter implements StylerAdapter
{

    /**
     * styles for first main font
     * 
     * @return String
     */
    protected function styleFirstTexts()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'text.main.first'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-first-first-texts'>"
                    . ".breadcrumbs > li:first-child a{color:" . $value . "}"
                    . ".breadcrumbs > li:last-child{color:" . $value . "}"
                    . ".cp-brand--primary .cp-brand__big{color:" . $value . "}"
                    . ".cp-brand--primary .minicolors-theme-ar-small:nth-child(1) .minicolors-swatch-color{background:" . $value . "}"
                    . "</style>\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.main.second'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-first-second-texts'>"
                    . ".cp-brand--primary .cp-brand__small{color:" . $value . " !important}"
                    . ".cp-brand--primary .minicolors-theme-ar-small:nth-child(2) .minicolors-swatch-color{background:" . $value . "}"
                    . "</style>\n";
        }
        return $return;
    }

    /**
     * styles for second font
     * 
     * @return String
     */
    protected function styleSecondTexts()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'text.secondary.first'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-second-first-texts'>"
                    . ".cp-brand--secondary .cp-brand__big{color:" . $value . " !important}"
                    . ".cp-brand--secondary .minicolors-theme-ar-small:nth-child(1) .minicolors-swatch-color{background:" . $value . "}"
                    . "</style>\n";
        }

        if (!is_null($value = array_get($this->colors, 'text.secondary.second'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-second-second-texts'>"
                    . ".cp-brand--secondary .cp-brand__small{color:" . $value . " !important}"
                    . ".cp-brand--secondary .minicolors-theme-ar-small:nth-child(2) .minicolors-swatch-color{background:" . $value . "}"
                    . "</style>\n";
        }



        return $return;
    }

    /**
     * styles for third font 
     * 
     * @return string
     */
    protected function styleThirdTexts()
    {
        $return = '';
        if (!is_null($value  = array_get($this->colors, 'text.background.first'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-third-first-texts'>"
                    . ".cp-brand--tetriary .cp-brand__big{color:" . $value . "}"
                    . ".cp-brand--tetriary .minicolors-theme-ar-small:nth-child(1) .minicolors-swatch-color{background:" . $value . "}"
                    . "</style>\n";
        }
        if (!is_null($value = array_get($this->colors, 'text.background.second'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return .= "<style data-desc='style-third-second-texts'>"
                    . ".cp-brand--tetriary .cp-brand__small{color:" . $value . " !important}"
                    . ".cp-brand--tetriary .minicolors-theme-ar-small:nth-child(2) .minicolors-swatch-color{background:" . $value . " }"
                    . "</style>\n";
        }
        return $return;
    }

    /**
     * styles for first colors container
     * 
     * @return String
     */
    protected function styleFirstContainer()
    {
        $return = '';
        foreach (['main.mod1', 'main.mod2', 'main.mod3'] as $index => $name) {
            if (!is_null($mod = array_get($this->colors, $name))) {
                $mod = starts_with($mod, '#') ? $mod : '#' . $mod;
                ++$index;
                $return.=".cp-brand--primary .cp-brand__sgl:nth-child({$index}){background-color:$mod}\n";
            }
        }
        if (!is_null($value = array_get($this->colors, 'main.value'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return.=".cp-brand--primary .cp-brand__preview{background-color:$value}\n";
        }
        return $return;
    }

    /**
     * styles for middle color container
     * 
     * @return String
     */
    protected function styleSecondContainer()
    {
        $return = '';
        foreach (['secondary.mod1', 'secondary.mod2', 'secondary.mod3'] as $index => $name) {
            if (!is_null($mod = array_get($this->colors, $name))) {
                $mod = starts_with($mod, '#') ? $mod : '#' . $mod;
                ++$index;
                $return.=".cp-brand--secondary .cp-brand__sgl:nth-child({$index}){background-color:$mod}\n";
            }
        }
        if (!is_null($value = array_get($this->colors, 'secondary.value'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return.=".cp-brand--secondary .cp-brand__preview{background-color:$value}\n";
        }
        return $return;
    }

    /**
     * styles for last color container
     * 
     * @return String
     */
    protected function styleThirdContainer()
    {
        $return = '';
        foreach (['background.mod1', 'background.mod2', 'background.mod3'] as $index => $name) {
            if (!is_null($mod = array_get($this->colors, $name))) {
                $mod = starts_with($mod, '#') ? $mod : '#' . $mod;
                ++$index;
                $return.=".cp-brand--tetriary .cp-brand__sgl:nth-child({$index}){background-color:$name}\n";
            }
        }
        if (!is_null($value = array_get($this->colors, 'background.value'))) {
            $value = starts_with($value, '#') ? $value : '#' . $value;
            $return.=".cp-brand--tetriary .cp-brand__preview{background-color:$value}\n";
        }
        return $return;
    }

    /**
     * concats partial form styles
     * 
     * @return String
     */
    public function style()
    {
        $container = '';
        if (strlen($first     = $this->styleFirstContainer()) > 0) {
            $container.= '<style data-desc="style-first-container">' . $first . '</style>';
        }
        if (strlen($second = $this->styleSecondContainer()) > 0) {
            $container.= '<style data-desc="style-second-container">' . $second . '</style>';
        }
        if (strlen($third = $this->styleThirdContainer()) > 0) {
            $container.= '<style data-desc="style-third-container">' . $third . '</style>';
        }

        $text = $this->styleFirstTexts() . $this->styleSecondTexts() . $this->styleThirdTexts();

        return $container . $text;
    }

}
