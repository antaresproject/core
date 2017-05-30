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


namespace Antares\Foundation\Http\Presenters;

use Antares\Foundation\Http\Form\Settings as SettingsForm;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;
use Antares\Contracts\Html\Form\Builder;
use Illuminate\Support\Fluent;

class Setting extends Presenter
{

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Construct a new Settings presenter.
     *
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Form View Generator for Setting Page.
     *
     * @param  Fluent  $model
     *
     * @return Builder
     */
    public function form($model)
    {
        $this->breadcrumb->onSettings($model);
        return new SettingsForm($model);
    }

}
