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

use Antares\Brands\Http\Presenters\Email as Presenter;
use Antares\Brands\Listener\BrandTemplateing as Listener;
use Antares\Brands\Repositories\BrandsRepository;
use Illuminate\Support\Facades\Input;

class Email
{

    /**
     * presenter instance
     *
     * @var Presenter 
     */
    protected $presenter;

    /**
     * instance of brand repository
     *
     * @var BrandsRepository
     */
    protected $repository;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param BrandsRepository $repository
     */
    public function __construct(Presenter $presenter, BrandsRepository $repository)
    {
        $this->presenter  = $presenter;
        $this->repository = $repository;
    }

    /**
     * shows email branding form
     * 
     * @param Listener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function email(Listener $listener, $id)
    {
        app('antares.asset')->container('antares/foundation::application')->add('webpack_brand_settings', '/webpack/view_brand_settings.js', ['app_cache']);
        $model = $this->repository->findById($id);
        $form  = $this->presenter->form($model);
        if (request()->isMethod('post') and ! $form->isValid()) {
            return $listener->updateFailedValidation($form->getMessageBag(), $id);
        }

        if (request()->isMethod('post')) {
            try {
                $this->repository->storeTemplate($model, Input::all());
                return $listener->updated($model);
            } catch (Exception $ex) {
                return $listener->updateFailed($model, $ex->getMessage());
            }
        }


        return $this->presenter->email($model);
    }

}
