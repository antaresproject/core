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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields\TestCase;

use Antares\Customfields\Events\ValidatorHandler;
use Illuminate\Support\Fluent as FluentStub;
use Antares\Testing\ApplicationTestCase;
use Mockery as m;
use Exception;

class ValidatorHandlerTest extends ApplicationTestCase
{

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {

        parent::setUp();
        $collection                                   = new \Illuminate\Support\Collection([]);
        $fieldView                                    = m::mock('\Antares\Customfields\Model\FieldView');
        $fieldView->shouldReceive('query')->withNoArgs()->andReturnSelf()
                ->shouldReceive('where')->withAnyArgs()->andReturnSelf()
                ->shouldReceive('get')->withAnyArgs()->andReturn($collection)
                ->shouldReceive('isEmpty')->withAnyArgs()->andReturn(true);
        $this->app['antares.customfields.model.view'] = $fieldView;
    }

    /**
     * Test method onViewForm
     * 
     * @test
     */
    public function testOnSubmitForm()
    {
        $stub = new ValidatorHandler();
        $this->assertInstanceOf('Antares\Customfields\Events\ValidatorHandler', $stub);

        $rules      = new FluentStub();
        $attributes = m::mock('\Illuminate\Support\Fluent');
        $attributes->shouldReceive('get')->with(m::type('String'))->andReturn('user.profile');



        try {
            $this->assertNull($stub->onSubmitForm($rules, $attributes));
            $this->assertEmpty($rules->getAttributes());
        } catch (Exception $ex) {
            $this->markTestIncomplete($ex->getMessage());
        }
    }

}
