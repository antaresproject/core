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


namespace Antares\Brands\Http\Presenters;

use Antares\Contracts\Html\Form\Builder;
use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Brands\Http\Breadcrumb\Breadcrumb;
use Illuminate\Database\Eloquent\Model;
use Antares\Brands\Http\Form\Form;

class Brand extends Presenter
{

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Construct a new Brand presenter.
     * 
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Form View Generator for Antares\Model\Brands.
     * 
     * @param  Model  $model
     * @return Builder
     */
    public function form(Model $model)
    {
        $this->breadcrumb->onBrandEdit($model);
        return new Form($model);
    }

}
