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

namespace Antares\Brands\Processor;

use Antares\Brands\Repositories\BrandsRepository;
use Antares\Brands\Http\Breadcrumb\Breadcrumb;
use Antares\Brands\Http\Form\Area as AreaForm;
use Antares\Brands\Model\Brands;

class Area
{

    /**
     * BrandsRepository instance
     *
     * @var Brands
     */
    protected $repository;

    /**
     * Breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * Construct
     * 
     * @param BrandsRepository $repository
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(BrandsRepository $repository, Breadcrumb $breadcrumb)
    {
        $this->repository = $repository;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * On edit area
     * 
     * @param mixed $brandId
     * @param mixed $templateId
     * @return \Illuminate\View\View
     */
    public function area($brandId, $templateId)
    {
        app('antares.asset')->container('antares/foundation::application')->add('webpack_brand_settings', '/webpack/view_brand_settings.js', ['app_cache']);
        $model    = $this->repository->findByIdAndTemplate($brandId, $templateId);
        $this->breadcrumb->onArea($model);
        $template = $model->templates->first();
        $form     = new AreaForm($template);

        if (request()->isMethod('post') && !empty($inputs = inputs())) {
            $template->fill($inputs);
            if (!$template->save()) {
                return redirect_with_message(url()->previous(), trans('antares/brands::messages.brand_template_has_not_been_updated'), 'error');
            }
            $url = extension_active('multibrand') ? 'antares::multibrand/index' : "antares::branding";
            return redirect_with_message(handles($url), trans('antares/brands::messages.brand_template_has_been_updated'), 'success');
        }
        return view('antares/foundation::brands.edit', ['form' => $form]);
    }

}
