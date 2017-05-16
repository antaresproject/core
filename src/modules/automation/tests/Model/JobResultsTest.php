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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Model\TestCase;

use Antares\Automation\Model\JobErrors;
use Antares\Automation\Model\JobResults as Model;
use Antares\Brands\Model\Brands;
use Antares\Support\Traits\Testing\EloquentConnectionTrait;
use Antares\Testing\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobsResultsTest extends TestCase
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
     * has timestamps
     */
    public function testHasTimestamps()
    {
        $this->assertFalse($this->model->timestamps);
    }

    /**
     * has valid morph class
     */
    public function testHasValidMorhClass()
    {
        $this->assertSame($this->model->getMorphClass(), 'Antares\Automation\Model\JobResults');
    }

    /**
     * has valid table name
     */
    public function testHasValidTableName()
    {
        $this->assertSame('tbl_job_results', $this->model->getTable());
    }

    /**
     * has valid relation to job errors table
     */
    public function testJobErrors()
    {
        $jobErrors = $this->model->jobErrors();
        $this->assertInstanceOf(BelongsTo::class, $jobErrors);
        $this->assertInstanceOf(JobErrors::class, $jobErrors->getModel());
    }

}
