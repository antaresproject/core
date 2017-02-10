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
 namespace Antares\Html\Table\TestCase;

use Mockery as m;
use Illuminate\Container\Container;
use Illuminate\Support\Fluent;
use Antares\Html\Table\Column;
use Antares\Html\Table\Grid;

class GridTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test instanceof Antares\Html\Table\Grid.
     *
     * @test
     */
    public function testInstanceOfGrid()
    {
        $app = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('antares/html::table', [])->andReturn([
                'empty' => 'No data',
                'view'  => 'foo',
            ]);

        $stub  = new Grid($app);
        $refl  = new \ReflectionObject($stub);
        $empty = $refl->getProperty('empty');
        $rows  = $refl->getProperty('rows');
        $view  = $refl->getProperty('view');

        $empty->setAccessible(true);
        $rows->setAccessible(true);
        $view->setAccessible(true);

        $this->assertInstanceOf('\Antares\Html\Table\Grid', $stub);
        $this->assertEquals('No data', $empty->getValue($stub));
        $this->assertEquals('foo', $view->getValue($stub));
        $this->assertEquals([], value($rows->getValue($stub)->attributes));
    }

    /**
     * Test Antares\Html\Table\Grid::with() method.
     *
     * @test
     */
    public function testWithMethod()
    {
        $stub = new Grid($this->getContainer());

        $mock = [new Fluent()];
        $stub->with($mock, false);

        $refl     = new \ReflectionObject($stub);
        $rows     = $refl->getProperty('rows');
        $model    = $refl->getProperty('model');
        $paginate = $refl->getProperty('paginate');

        $rows->setAccessible(true);
        $model->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertEquals($mock, $stub->rows());
        $this->assertEquals($mock, $model->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
        $this->assertTrue(isset($stub->model));
    }

    /**
     * Test Antares\Html\Table\Grid::with() method given a
     * Illuminate\Pagination\Paginator instance.
     *
     * @test
     */
    public function testWithMethodGivenPaginatorInstance()
    {
        $expected = ['foo'];
        $stub     = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Contracts\Pagination\Paginator');
        $model->shouldReceive('items')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Antares\Html\Table\Grid::with() method given a paginable
     * instance.
     *
     * @test
     */
    public function testWithMethodGivenModelBuilderInstance()
    {
        $expected = ['foo'];
        $stub     = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Database\Eloquent\Builder')->makePartial();
        $model->shouldReceive('paginate')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Antares\Html\Table\Grid::with() method given a
     * Illuminate\Contracts\Support\Arrayable instance.
     *
     * @test
     */
    public function testWithMethodGivenArrayableInterfaceInstance()
    {
        $expected = ['foo'];
        $stub     = new Grid($this->getContainer());

        $model = m::mock('\Illuminate\Contracts\Support\Arrayable');
        $model->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Antares\Html\Table\Grid::with() method given a
     * Query Builder instance when paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenPaginated()
    {
        $expected = ['foo'];
        $stub     = new Grid($this->getContainer());

        $model     = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('paginate')->once()->with(25, ['*'], 'page')->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->paginate(25);
        $stub->with($model);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Antares\Html\Table\Grid::with() method given a
     * Query Builder instance when not paginated.
     *
     * @test
     */
    public function testWithMethodGivenQueryBuilderInstanceWhenNotPaginated()
    {
        $expected = ['foo'];
        $stub     = new Grid($this->getContainer());

        $model     = m::mock('\Illuminate\Database\Query\Builder');
        $arrayable = m::mock('\Illuminate\Contracts\Support\Arrayable');

        $model->shouldReceive('get')->once()->andReturn($arrayable);
        $arrayable->shouldReceive('toArray')->once()->andReturn($expected);

        $stub->with($model);
        $stub->paginate(null);

        $this->assertEquals($expected, $stub->rows());
    }

    /**
     * Test Antares\Html\Table\Grid::with() method throws an exceptions
     * when $model can't be converted to array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testWithMethodThrowsAnException()
    {
        $stub = new Grid($this->getContainer());

        $model = 'Foo';

        $stub->with($model, false);

        $stub->rows();
    }

    /**
     * Test Antares\Html\Table\Grid::layout() method.
     *
     * @test
     */
    public function testLayoutMethod()
    {
        $stub = new Grid($this->getContainer());

        $refl = new \ReflectionObject($stub);
        $view = $refl->getProperty('view');
        $view->setAccessible(true);

        $stub->layout('horizontal');
        $this->assertEquals('antares/html::table.horizontal', $view->getValue($stub));

        $stub->layout('vertical');
        $this->assertEquals('antares/html::table.vertical', $view->getValue($stub));

        $stub->layout('foo');
        $this->assertEquals('foo', $view->getValue($stub));
    }

    /**
     * Test Antares\Html\Table\Grid::of() method.
     *
     * @test
     */
    public function testOfMethod()
    {
        $me   = $this;
        $stub = new Grid($this->getContainer());

        $stub->with([
            new Fluent(['foo1' => 'Foo1']),
        ], false);

        $expected = [
            new Column([
                'id'         => 'id',
                'label'      => 'Id',
                'value'      => function ($row) {
                    return $row->id;
                },
                'headers'    => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
            new Column([
                'id'         => 'foo1',
                'label'      => 'Foo1',
                'value'      => 'Foo1 value',
                'headers'    => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
            new Column([
                'id'         => 'foo2',
                'label'      => 'Foo2',
                'value'      => 'Foo2 value',
                'headers'    => [],
                'attributes' => function ($row) {
                    return [];
                },
            ]),
        ];

        $stub->column('id');

        $stub->column(function ($c) {
            $c->id('foo1')->label('Foo1');
            $c->value('Foo1 value');
        });

        $stub->column('Foo2', 'foo2')->value('Foo2 value');

        $stub->attributes = ['class' => 'foo'];

        $output = $stub->of('id', function ($fluent) use ($me) {
            $me->assertInstanceOf('\Illuminate\Support\Fluent', $fluent);
        });

        $this->assertEquals('Id', $output->label);
        $this->assertEquals('id', $output->id);
        $this->assertEquals([], call_user_func($output->attributes, new Fluent()));
        $this->assertEquals(5, call_user_func($output->value, new Fluent(['id' => 5])));

        $this->assertEquals(['class' => 'foo'], $stub->attributes);
        $this->assertEquals($expected, $stub->columns());
    }

    /**
     * Test Antares\Html\Table\Grid::of() method throws exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testOfMethodThrowsException()
    {
        $stub = new Grid($this->getContainer());
        $stub->of('id');
    }

    /**
     * Test Antares\Html\Table\Grid::paginate() method.
     *
     * @test
     */
    public function testPaginateMethod()
    {
        $stub = new Grid($this->getContainer());
        $refl = new \ReflectionObject($stub);

        $perPage  = $refl->getProperty('perPage');
        $paginate = $refl->getProperty('paginate');

        $perPage->setAccessible(true);
        $paginate->setAccessible(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(25);

        $this->assertEquals(25, $perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(2.5);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(-10);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));

        $stub->paginate(true);

        $this->assertNull($perPage->getValue($stub));
        $this->assertTrue($paginate->getValue($stub));

        $stub->paginate(false);

        $this->assertNull($perPage->getValue($stub));
        $this->assertFalse($paginate->getValue($stub));
    }

    /**
     * Test Antares\Html\Table\Grid::searchable() method.
     *
     * @test
     */
    public function testSearchableMethod()
    {
        $attributes = ['email', 'fullname'];
        $app        = $this->getContainer();

        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('q')->andReturn('antares*');

        $stub = m::mock('\Antares\Html\Table\Grid[setupWildcardQueryFilter]', [$app])
                    ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $stub->shouldReceive('setupWildcardQueryFilter')->once()->with($model, 'antares*', $attributes)->andReturnNull();

        $stub->with($model);

        $this->assertNull($stub->searchable($attributes));

        $this->assertEquals($attributes, $stub->get('search.attributes'));
        $this->assertEquals('q', $stub->get('search.key'));
        $this->assertEquals('antares*', $stub->get('search.value'));
    }

    /**
     * Test Antares\Html\Table\Grid::searchable() method
     * throws exception when model is not a query builder.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSearchableMethodThrowsException()
    {
        $attributes = ['email', 'fullname'];

        $stub = new Grid($this->getContainer());

        $stub->with('Foo');

        $stub->searchable($attributes);
    }

    /**
     * Test Antares\Html\Table\Grid::sortable() method.
     *
     * @test
     */
    public function testSortableMethod()
    {
        $app            = $this->getContainer();
        $app['request'] = $request = m::mock('\Illuminate\Http\Request');

        $request->shouldReceive('input')->once()->with('order_by')->andReturn('email')
            ->shouldReceive('input')->once()->with('direction')->andReturn('desc');

        $stub = m::mock('\Antares\Html\Table\Grid[setupBasicQueryFilter]', [$app])
            ->shouldAllowMockingProtectedMethods();

        $model = m::mock('\Illuminate\Database\Query\Builder');

        $stub->shouldReceive('setupBasicQueryFilter')->once()
            ->with($model, ['order_by' => 'email', 'direction' => 'desc', 'columns' => ['only' => ['email'], 'except' => ['fullname']]])
            ->andReturnNull();

        $stub->with($model);

        $this->assertNull($stub->sortable(['only' => ['email'], 'except' => ['fullname']], 'order_by', 'direction'));

        $this->assertEquals(['key'  => 'order_by', 'value' => 'email'], $stub->get('filter.order_by'));
        $this->assertEquals(['key'  => 'direction', 'value' => 'desc'], $stub->get('filter.direction'));
        $this->assertEquals(['only' => ['email'], 'except' => ['fullname']], $stub->get('filter.columns'));
    }

    /**
     * Test Antares\Html\Table\Grid::sortable() method
     * throws exception when model is not a query builder.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSortableMethodThrowsException()
    {
        $attributes = ['email', 'fullname'];

        $stub = new Grid($this->getContainer());

        $stub->with('Foo');

        $stub->sortable($attributes);
    }

    /**
     * Test Antares\Html\Table\Grid::attributes() method.
     *
     * @test
     */
    public function testAttributesMethod()
    {
        $stub = new Grid($this->getContainer());

        $refl       = new \ReflectionObject($stub);
        $attributes = $refl->getProperty('attributes');
        $attributes->setAccessible(true);

        $stub->attributes(['class' => 'foo']);

        $this->assertEquals(['class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['class' => 'foo'], $stub->attributes());

        $stub->attributes('id', 'foobar');

        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $attributes->getValue($stub));
        $this->assertEquals(['id' => 'foobar', 'class' => 'foo'], $stub->attributes());
    }

    /**
     * Test Antares\Html\Table\Grid magic method __call() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodCallThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidMethod();
    }

    /**
     * Test Antares\Html\Table\Grid magic method __get() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodGetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidProperty;
    }

    /**
     * Test Antares\Html\Table\Grid magic method __set() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        $stub->invalidProperty = ['foo'];
    }

    /**
     * Test Antares\Html\Table\Grid magic method __set() throws
     * exception when $values is not an array.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
    {
        $stub = new Grid($this->getContainer());

        $stub->attributes = 'foo';
    }

    /**
     * Test Antares\Html\Table\Grid magic method __isset() throws
     * exception.
     *
     * @expectedException \InvalidArgumentException
     */
    public function testMagicMethodIssetThrowsException()
    {
        $stub = new Grid($this->getContainer());

        isset($stub->invalidProperty) ? true : false;
    }

    /**
     * Get app container.
     *
     * @return Container
     */
    protected function getContainer()
    {
        $app                                           = new Container();
        $app['Illuminate\Contracts\Config\Repository'] = $config = m::mock('\Illuminate\Contracts\Config\Repository');

        $config->shouldReceive('get')->once()
            ->with('antares/html::table', [])->andReturn([]);

        return $app;
    }
}
