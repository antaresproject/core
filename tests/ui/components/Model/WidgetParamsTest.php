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
use Antares\UI\UIComponents\Model\ComponentParams as Stub;
use Antares\UI\UIComponents\Model\Components;
use Antares\Testing\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class WidgetParamsTest extends TestCase
{

    use EloquentConnectionTrait;
    use DatabaseTransactions;

    /**
     * Test Antares\Widgets\Model\WidgetParams::fillable params
     *
     * @test
     */
    public function testValidFillable()
    {
        $model = new Stub();
        $this->addMockConnection($model);
        $this->assertEquals($model->getFillable(), array('wid', 'uid', 'name', 'brand_id', 'resource', 'data'));
    }

    /**
     * Test Antares\Widgets\Model\WidgetParams::widgets() method.
     *
     * @test
     */
    public function testWidgets()
    {
        $model  = new Stub();
        $this->addMockConnection($model);
        $params = $model->widget();
        $this->assertSame('BelongsTo', class_basename($params));
    }

    /**
     * test updating tree structure
     * 
     * @test
     */
    public function testSaveTree()
    {
        $widget = Components::query()->create([
            'type_id' => 1,
            'name' => 'Testable component',
        ]);

        $model = Stub::query()->create([
            'wid' => $widget->id,
            'uid' => 1,
            'brand_id' => 1,
            'resource' => '/',
            'name' => 'test_component',
            'data' => [
                'fixed_width' => 100, // this will be ignored
                'some_dump_key' => 'foo',
            ],
        ]);

        $this->assertEquals(['some_dump_key' => 'foo'], $model->data);
    }

}
