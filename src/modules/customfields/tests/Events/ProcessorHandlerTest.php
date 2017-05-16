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

use Antares\Customfields\Events\ProcessorHandler;
use Illuminate\Support\Facades\Input;
use Antares\Testing\TestCase;
use Mockery as m;

class ProcessorHandlerTest extends TestCase
{

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @see parent::teraDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * test method onSave
     */
    public function testOnSave()
    {
        $eloquent  = m::mock('\Antares\Model\Eloquent');
        $fieldView = m::mock('\Antares\Customfields\Model\FieldView');
        $fieldData = m::mock('\Antares\Customfields\Model\FieldData');
        $inputStub = new Input();


        $stub = new ProcessorHandler($inputStub, $fieldView, $fieldData, 1);

        $namespace = 'user.profile';
        $this->assertFalse($stub->onSave($eloquent, $namespace));
    }

}
