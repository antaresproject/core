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

use Antares\Customfields\Model\Field;
use Antares\Testing\TestCase;

class FieldTest extends TestCase
{

    /**
     * @var Antares\Customfields\Model\Field 
     */
    private $stub;

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new Field();
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
        $this->assertSame($this->stub->getMorphClass(), 'Antares\Customfields\Model\Field');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_fields', $this->stub->getTable());
    }

    /**
     * has valid fillable configuration
     */
    public function testFillable()
    {
        $this->assertSame($this->stub->fillable, ['brand_id', 'group_id', 'type_id', 'name', 'label', 'placeholder', 'value', 'description', 'imported', 'force_display', 'additional_attributes']);
    }

    /**
     * test groups method
     */
    public function testGroups()
    {

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $this->stub->groups());
        $this->assertInstanceOf('Antares\Customfields\Model\FieldGroup', $this->stub->groups()->getModel());
    }

    /**
     * test categories method
     */
    public function testCategories()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $this->stub->categories());
        $this->assertInstanceOf('Antares\Customfields\Model\FieldCategory', $this->stub->categories()->getModel());
    }

    /**
     * test types method
     */
    public function types()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $this->stub->types());
        $this->assertInstanceOf('Antares\Customfields\Model\FieldType', $this->stub->types()->getModel());
    }

    /**
     * test getFlattenValidators method
     */
    public function testGetFlattenValidators()
    {
        $this->assertEmpty($this->stub->getFlattenValidators());
    }

}
