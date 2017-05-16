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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Templates\Model\TestCase;

use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Notifications\Model\Notifications as Model;
use Antares\Notifications\Model\NotificationCategory;
use Antares\Notifications\Model\NotificationContents;
use Antares\Notifications\Model\NotificationSeverity;
use Antares\Notifications\Model\NotificationsStack;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Antares\Notifications\Model\NotificationTypes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Antares\Brands\Model\Brands;
use Antares\Testing\TestCase;

class NotificationsTest extends TestCase
{

    use EloquentConnectionTrait;

    /**
     * @var Brands
     */
    private $model;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        $model       = new Model();
        $this->addMockConnection($model);
        $this->model = $model;
    }

    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Has timestamps
     * 
     * @test
     */
    public function testHasTimestamps()
    {
        $this->assertFalse($this->model->timestamps);
    }

    /**
     * Has valid morph class
     * 
     * @test
     */
    public function testHasValidMorhClass()
    {
        $this->assertSame($this->model->getMorphClass(), 'Antares\Notifications\Model\Notifications');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_notifications', $this->model->getTable());
    }

    /**
     * Has valid relation to severity table
     * 
     * @test
     */
    public function testSeverity()
    {
        $severity = $this->model->severity();
        $this->assertInstanceOf(HasOne::class, $severity);
        $this->assertInstanceOf(NotificationSeverity::class, $severity->getModel());
    }

    /**
     * Has valid relation to stack table
     * 
     * @test
     */
    public function testStack()
    {
        $stack = $this->model->stack();
        $this->assertInstanceOf(HasMany::class, $stack);
        $this->assertInstanceOf(NotificationsStack::class, $stack->getModel());
    }

    /**
     * Has valid relation to contents table
     * 
     * @test
     */
    public function testContents()
    {
        $contents = $this->model->contents();
        $this->assertInstanceOf(HasMany::class, $contents);
        $this->assertInstanceOf(NotificationContents::class, $contents->getModel());
    }

    /**
     * Has valid relation to category table
     * 
     * @test
     */
    public function testCategory()
    {
        $category = $this->model->category();
        $this->assertInstanceOf(HasOne::class, $category);
        $this->assertInstanceOf(NotificationCategory::class, $category->getModel());
    }

    /**
     * Has valid relation to type table
     * 
     * @test
     */
    public function testType()
    {
        $type = $this->model->type();
        $this->assertInstanceOf(HasOne::class, $type);
        $this->assertInstanceOf(NotificationTypes::class, $type->getModel());
    }

}
