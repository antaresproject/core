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
 * @package    Widgets
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Widgets\Model\Tests;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\UI\UIComponents\Model\ComponentTypes as Stub;
use Antares\Testing\TestCase;

class WidgetTypesTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * Test Antares\Widgets\Model\WidgetTypes::fillable params
     *
     * @test
     */
    public function testValidFillable()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $this->assertEquals($model->getFillable(), array('name', 'slug', 'description'));
    }

    /**
     * Test Antares\Widgets\Model\WidgetTypes::widgets() method.
     *
     * @test
     */
    public function testWidgetParams()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $this->assertSame('HasMany', class_basename($model->widgets()));
    }

}
