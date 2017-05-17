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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Model\TestCase;

use Antares\Testing\ApplicationTestCase;
use Antares\Model\Eloquent;

class EloquentTest extends ApplicationTestCase
{

    public function testIsSoftDeletingMethod()
    {
        $stub1 = new SoftDeletingModel();
        $stub2 = new NoneSoftDeletingModel();
        $stub3 = new ForceDeletingModel();

        $this->assertTrue($stub1->isSoftDeleting());
        $this->assertFalse($stub2->isSoftDeleting());
        $this->assertFalse($stub3->isSoftDeleting());
    }

}

class SoftDeletingModel extends Eloquent
{

    protected $forceDeleting = false;

}

class NoneSoftDeletingModel extends Eloquent
{
    
}

class ForceDeletingModel extends Eloquent
{

    protected $forceDeleting = true;

}
