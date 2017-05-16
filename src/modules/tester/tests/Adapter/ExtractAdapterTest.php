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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Adapter\Tests;

use Antares\Tester\Adapter\ExtractAdapter as Stub;
use Antares\Tester\TesterServiceProvider;
use Antares\Testing\ApplicationTestCase;
use Illuminate\Session\SessionManager;
use Mockery as m;
use Exception;

class ExtractAdapterTest extends ApplicationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->addProvider(TesterServiceProvider::class);
        parent::setUp();
    }

    /**
     * Test Antares\Tester\Adapter\ExtractAdapter::generateScripts() method.
     *
     * @test
     */
    public function testGenerateScripts()
    {
        $session              = m::mock(SessionManager::class);
        $session->shouldReceive('token')->withNoArgs()->andReturn(str_random(10));
        $this->app['session'] = $session;
        $stub                 = new Stub();
        $this->assertNull($stub->generateScripts(['id' => 'test-form']));
        $this->assertTrue(str_contains(app('antares.asset')->container($this->app->make('config')->get('antares/tester::container'))->inline(), 'text/javascript'));
    }

    /**
     * Test Antares\Tester\Adapter\ExtractAdapter::extractForm() method.
     *
     * @test
     */
    public function testExecptionThrowsWhenExtractForm()
    {
        $stub = new Stub();

        try {
            $stub->extractForm(ExtractAdapterTest::class);
        } catch (Exception $e) {
            $this->assertSame($e->getMessage(), 'Undefined offset: 1');
            $this->assertEquals(0, $e->getCode());
        }
    }

}
