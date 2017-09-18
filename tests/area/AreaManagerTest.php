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

namespace Antares\Area\Tests;

use Antares\Area\Middleware\AreasCollection;
use Antares\Model\User;
use Illuminate\Http\Request;
use Mockery as m;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Antares\Testbench\TestCase;
use Antares\Area\Model\Area;
use Antares\Area\Contracts\AreaManagerContract;
use Antares\Area\Contracts\AreaContract;
use Antares\Area\AreaManager;

class AreaManagerTest extends TestCase
{

    /**
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * @var
     */
    protected $request;

    /**
     * @var m
     */
    protected $auth;

    public function setUp() {
        parent::setUp();

        $this->app = $this->createApplication();

        $this->request  = m::mock(Request::class);
        $this->auth     = m::mock(AuthFactory::class);

        $this->app['translator'] = m::mock('\Illuminate\Translation\Translator')->makePartial();
    }

    /**
     * @param array $config
     * @return AreaManager
     */
    protected function getManager(array $config = []) {
        return new AreaManager($this->request, $this->auth, $config);
    }

    public function testContract()
    {
        $this->assertInstanceOf(AreaManagerContract::class, $this->getManager());
    }

    public function testWithoutAreas()
    {
        $areaManager = $this->getManager();
        $areas       = $areaManager->getAreas();

        $this->assertInstanceOf(AreasCollection::class, $areas);
        $this->assertCount(0, $areas);
    }

    public function testAreas()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);
        $areas       = $areaManager->getAreas();

        $this->assertInstanceOf(AreasCollection::class, $areas);
        $this->assertCount(2, $areas);
        $this->assertInstanceOf(AreaContract::class, head($areas->all()));
    }

    public function testGetAreaById()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);
        $adminArea  = new Area('admin', 'Admin');
        $client     = new Area('client', 'Client');

        $this->assertEquals($adminArea, $areaManager->getById('admin'));
        $this->assertEquals($client, $areaManager->getById('client'));
        $this->assertNull($areaManager->getById('dump'));
    }

    public function testGetDefault()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);

        $this->assertEquals('client', $areaManager->getDefault()->getId());
    }

    public function testGetAreaByIdOrDefault()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);

        $this->assertEquals('admin', $areaManager->getByIdOrDefault('admin')->getId());
        $this->assertEquals('client', $areaManager->getByIdOrDefault('dump')->getId());
    }

    public function testHasAreaInUriAsEmptySegment()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $this->request->shouldReceive('segment')->once()->andReturnNull();

        $areaManager = $this->getManager($config);

        $this->assertFalse($areaManager->hasAreaInUri());
    }

    public function testHasAreaInUri()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $this->request->shouldReceive('segment')->once()->andReturn('client');

        $areaManager = $this->getManager($config);

        $this->assertTrue($areaManager->hasAreaInUri());
    }

    public function testGetCurrentAreaWithValidUri()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);

        $this->request->shouldReceive('segment')->once()->andReturn('admin');

        $this->assertEquals('admin', $areaManager->getCurrentArea()->getId());
    }

    public function testGetCurrentAreaWithInvalidUri()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);

        $this->auth->shouldReceive('check')->once()->andReturn(false);
        $this->request->shouldReceive('segment')->once()->andReturn('dump');
        $this->assertEquals('client', $areaManager->getCurrentArea()->getId());
    }

    public function testGetCurrentAreaFromUser()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',
        ];

        $areaManager = $this->getManager($config);

        $user = m::mock(User::class)->makePartial()->shouldReceive('getArea')->once()->andReturn('admin')->getMock();

        $this->auth->shouldReceive('check')->once()->andReturn(true);
        $this->auth->shouldReceive('user')->once()->andReturn($user);

        $this->request->shouldReceive('segment')->once()->andReturn('dump');

        $this->assertEquals('admin', $areaManager->getCurrentArea()->getId());
    }

    public function testGetCurrentAreaFromUserAsDefault()
    {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',

            'routes'  => [
                'frontend' => [
                    'client',
                ],
                'backend'  => [
                    'admin', 'reseller'
                ]
            ],
        ];

        $areaManager = $this->getManager($config);

        $user = m::mock(User::class)->makePartial()->shouldReceive('getArea')->once()->andReturn('dump')->getMock();

        $this->auth->shouldReceive('check')->once()->andReturn(true);
        $this->auth->shouldReceive('user')->once()->andReturn($user);

        $this->request->shouldReceive('segment')->once()->andReturn('dump');

        $this->assertEquals('client', $areaManager->getCurrentArea()->getId());
    }

    public function testGetFrontendAreas() {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',

            'routes'  => [
                'frontend' => [
                    'client',
                ],
                'backend'  => [
                    'admin', 'reseller'
                ]
            ],
        ];

        $areaManager = $this->getManager($config);

        foreach($areaManager->getFrontendAreas()->all() as $area) {
            $this->assertEquals('client', $area->getId());
        }
    }

    public function testGetBackendAreas() {
        $config = [
            'areas'   => [
                'admin'    => 'Admin',
                'client'   => 'Client',
            ],
            'default' => 'client',

            'routes'  => [
                'frontend' => [
                    'client',
                ],
                'backend'  => [
                    'admin', 'reseller'
                ]
            ],
        ];

        $areaManager = $this->getManager($config);

        $this->assertCount(1, $areaManager->getFrontendAreas()->all()); // reseller area is not signed

        foreach($areaManager->getBackendAreas()->all() as $area) {
            $this->assertEquals('admin', $area->getId());
        }
    }

}
