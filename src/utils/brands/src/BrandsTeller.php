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

namespace Antares\Brands;

use Illuminate\Container\Container;
use Antares\Brands\Contracts\BrandsRepositoryContract;

class BrandsTeller
{

    /**
     *
     * @var Container
     */
    protected $app;

    /**
     *
     * @var BrandsRepositoryContract
     */
    protected $repository;

    /**
     * 
     * @param Container $app
     * @param BrandsRepository $repository
     */
    public function __construct(Container $app, BrandsRepositoryContract $repository)
    {
        $this->app        = $app;
        $this->repository = $repository;
    }

    /**
     * saving default brand
     * 
     * @param int $brandId
     * @return \Antares\Brands\BrandsTeller
     */
    public function setDefaultBrandById($brandId)
    {
        $this->repository->setDefaultBrandById($brandId);

        $memory = $this->app->make('antares.memory')->make('primary');
        $memory->put('brand.default', $brandId);
        $memory->update();

        return $this;
    }

    /**
     * gets default brand id
     * 
     * @return int
     */
    public function getDefaultBrandId()
    {
        return $this->repository->findDefault()->id;
    }

}
