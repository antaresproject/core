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

use Antares\Brands\Http\Form\Email as EmailForm;
use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Brands\Http\Breadcrumb\Breadcrumb;
use Illuminate\Database\Eloquent\Model;

class Email extends Presenter
{

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Construct a new email branding presenter.
     * 
     * @param Breadcrumb $breadcrumb   
     */
    public function __construct(Breadcrumb $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * view email branding
     * 
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function email(Model $model)
    {
        view()->share('pageId', 'page-email-settings');
        publish(null, ['/packages/core/js/brand-form.js', '/packages/core/js/brand-colors.js']);
        $this->breadcrumb->onBrandEmailEdit($model);
        $form = $this->form($model);
        return view('antares/foundation::brands.email', compact('form'));
    }

    /**
     * creates form instance
     * 
     * @param Model $model
     * @return \Antares\Multibrand\Http\Form
     */
    public function form(Model $model)
    {
        return new EmailForm($model);
    }

}
