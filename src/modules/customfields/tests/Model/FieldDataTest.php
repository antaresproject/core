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

use Antares\Customfields\Model\FieldData;
use Antares\Testing\TestCase;

class FieldDataTest extends TestCase
{

    /**
     * @var Antares\Customfields\Model\FieldData
     */
    private $stub;

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new FieldData();
    }

    /**
     * @see parent::teraDown
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * has timestamps
     */
    public function testHasTimestamps()
    {
        $this->assertFalse($this->stub->timestamps);
    }

    /**
     * has valid morph class
     */
    public function testHasValidMorhClass()
    {
        $this->assertSame($this->stub->getMorphClass(), 'Antares\Customfields\Model\FieldData');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_fields_data', $this->stub->getTable());
    }

    /**
     * test has valid fillable params
     */
    public function testHasValidFillableConfiguration()
    {
        $this->assertSame($this->stub->fillable, ['user_id', 'namespace', 'foreign_id', 'field_id', 'field_class', 'option_id', 'data']);
    }

    /**
     * test has valid relation
     */
    public function testFieldRelation()
    {
        $relation = $this->stub->field();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $relation);
        $this->assertInstanceOf('Antares\Customfields\Model\FieldView', $relation->getModel());
    }

    /**
     * test has valid user relation
     */
    public function testUserRelation()
    {
        $relation = $this->stub->user();
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $relation);
        $this->assertInstanceOf('Antares\Model\User', $relation->getModel());
    }

}
