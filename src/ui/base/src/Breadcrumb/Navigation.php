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

namespace Antares\Breadcrumb;

use Illuminate\Container\Container;
use DaveJamesMiller\Breadcrumbs\Manager as Breadcrumbs;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Routing\UrlGenerator;

abstract class Navigation
{

    /**
     *
     * @var Breadcrumbs
     */
    protected $breadcrumbs;

    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     *
     * @var View
     */
    protected $view;

    public function __construct(Container $container, UrlGenerator $url, View $view)
    {
        $this->breadcrumbs = $container->make('breadcrumbs');
        $this->url         = $url;
        $this->view        = $view;
    }

    protected function shareOnView($name)
    {
        if (php_sapi_name() === 'cli') {
            return;
        }
        $this->view->share('breadcrumbs', $this->breadcrumbs->render($name));
    }

}
