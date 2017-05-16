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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Presenters;

use Antares\Logger\Contracts\GeneratorPresenter as PresenterContract;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Contracts\Container\Container;

class GeneratorPresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container, \Antares\Logger\Http\Breadcrumb\Breadcrumb $breadcrumb)
    {
        $this->container  = $container;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * publish html report
     * 
     * @param Eloquent $model
     * @return \Illuminate\View\View
     */
    public function publishHtml(Eloquent $model)
    {
        $this->breadcrumb->onPreviewHtmlReport($model);
        return $this->view('html', ['model' => $model]);
    }

    /**
     * preview html report
     * 
     * @param String $html
     * @return \Illuminate\View\View
     */
    public function html($html)
    {
        return $this->view('preview', ['html' => $html]);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     *
     * @return \Illuminate\View\View
     */
    protected function view($view, $data = [], $mergeData = [])
    {
        return view('antares/logger::admin.generator.' . $view, $data, $mergeData);
    }

}
