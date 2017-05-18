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

namespace Antares\Brands\TestCase;

use Antares\Brands\Repositories\BrandsRepository;
use Antares\Testing\ApplicationTestCase;
use Antares\Brands\BrandsTeller;
use Mockery as m;

class BrandsTellerTest extends ApplicationTestCase
{

    /**
     * @var BrandsTeller
     */
    private $stub;

    /**
     * @overwrite 
     * @see parent
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new BrandsTeller($this->app, app(BrandsRepository::class));
    }

    /**
     * Create a new instance of Brandstelling.
     * @test
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('Antares\Brands\BrandsTeller', $this->stub);
    }

    /**
     * saving default brand
     * @test
     */
    public function testDefaultBrandById()
    {

        $this->app['antares.app'] = m::mock('\Antares\Contracts\Foundation\Foundation');
        $this->assertInstanceOf('Antares\Brands\BrandsTeller', $this->stub->setDefaultBrandById(1));
    }

    /**
     * gets default brand id
     * @test
     */
    public function testGetDefaultBrandId()
    {
        $this->assertTrue(is_numeric($this->stub->getDefaultBrandId()));
    }

}
