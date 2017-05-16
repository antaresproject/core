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

use Antares\Customfields\Model\FieldGroup;
use Antares\Testing\TestCase;

class FieldGroupTest extends TestCase
{

    /**
     * @var Antares\Customfields\Model\FieldGroup 
     */
    private $stub;

    /**
     * @see parent::setUp
     */
    public function setUp()
    {
        parent::setUp();
        $this->stub = new FieldGroup();
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
        $this->assertSame($this->stub->getMorphClass(), 'Antares\Customfields\Model\FieldGroup');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_fields_groups', $this->stub->getTable());
    }

    /**
     * test category method
     */
    public function testCategory()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasOne', $this->stub->category());
        $this->assertInstanceOf('Antares\Customfields\Model\FieldCategory', $this->stub->category()->getModel());
    }

}
