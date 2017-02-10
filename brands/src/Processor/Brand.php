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

use Antares\Brands\Listener\BrandUpdater as BrandUpdaterListener;
use Antares\Brands\Http\Presenters\Brand as Presenter;
use Antares\Brands\Contracts\BrandsRepositoryContract;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Log;
use Antares\Brands\BrandsTeller;
use Illuminate\Events\Dispatcher;
use Exception;

class Brand extends Processor
{

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var BrandsRepositoryContract
     */
    protected $repository;

    /**
     *
     * @var BrandsTeller
     */
    protected $brandsTeller;

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Users\Http\Presenters\User  $presenter
     * @param  BrandsRepository  $repository
     * @param  Dispatcher  $dispatcher
     */
    public function __construct(Presenter $presenter, BrandsRepositoryContract $repository, Dispatcher $dispatcher, BrandsTeller $brandsTeller)
    {
        $this->presenter    = $presenter;
        $this->repository   = $repository;
        $this->dispatcher   = $dispatcher;
        $this->brandsTeller = $brandsTeller;
    }

    /**
     * grid default view
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return $this->presenter->table();
    }

    /**
     * View edit brand page.
     * @param  BrandUpdaterListener  $listener
     * @param  string|int  $id
     *
     * @return mixed
     */
    public function edit(BrandUpdaterListener $listener, $id)
    {
        $brand = $this->repository->findById($id);

        if (!$brand) {
            return $listener->abortWhenBrandMismatched();
        }
        $form = $this->presenter->form($brand, 'update');
        $this->fireEvent('form', [$brand, $form]);
        $this->dispatcher->fire("antares.form: foundation.brand", [$brand, $form, "foundation.brand"]);

        return $listener->showBrandChanger(compact('eloquent', 'form'));
    }

    /**
     * Update a brand.
     *
     * @param  BrandUpdaterListener  $listener
     * @param  string|int  $id
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(BrandUpdaterListener $listener, $id, array $input)
    {
        $brand = $this->repository->findById($id);
        if (!$brand) {
            return $listener->abortWhenBrandMismatched();
        }
        $form = $this->presenter->form($brand);

        if (!$form->isValid()) {
            return $listener->updateBrandFailedValidation($form->getMessageBag(), $id);
        }
        $this->fireEvent('updating', [$brand]);
        $this->fireEvent('saving', [$brand]);
        try {
            $this->repository->update($brand, $input);
            $this->dispatcher->fire("antares.form: foundation.brand.save", [$brand, 'namespace' => 'foundation.brand']);
            $this->fireEvent('updated', [$brand]);
            $this->fireEvent('saved', [$brand]);
            return $listener->brandUpdated($brand);
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->updateBrandFailed($brand, ['error' => $e->getMessage()]);
        }
    }

    /**
     * Fire Event related to eloquent process.
     * @param  string  $type
     * @param  array   $parameters
     * @return void
     */
    protected function fireEvent($type, array $parameters = [])
    {
        $this->dispatcher->fire("antares.{$type}: brands", $parameters);
    }

}
