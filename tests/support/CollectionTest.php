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

namespace Antares\Support\TestCase;

use Antares\Support\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Antares\Support\Collection::toCsv() method.
     *
     * @test
     */
    public function testToCsvMethod()
    {
        $stub = new Collection([
            ['id' => 1, 'name' => 'Mior Muhammad Zaki'],
            ['id' => 2, 'name' => 'Taylor Otwell'],
            ['id' => 3, 'name' => 'Antares'],
        ]);

        $expected = <<<EXPECTED
id,name
1,"Mior Muhammad Zaki"
2,"Taylor Otwell"
3,Antares
EXPECTED;
        $this->assertEquals(str_replace("\r", "", $expected) . "\n", $stub->toCsv());
    }

}
