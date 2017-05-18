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

use Antares\Html\Control\RemoteSelect;
use Twig_SimpleFunction;
use Twig_Extension;

class Html extends Twig_Extension
{

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_Html';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('anchor', function ($url, $title, $attributes = []) {
                return anchor($url, $title, $attributes);
            }),
            new Twig_SimpleFunction('url_external', function ($to) {
                return config('app.url') . str_replace(app('url')->to('/'), '', handles($to));
            }),
            new Twig_SimpleFunction('remote_select', function ($params = null) {
                return app(RemoteSelect::class)->setParams($params)->render();
            }),
            new Twig_SimpleFunction('tooltip', function ($params = null) {
                echo tooltip($params);
                return '';
            })
        ];
    }

}
