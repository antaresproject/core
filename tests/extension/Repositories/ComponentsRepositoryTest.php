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

namespace Antares\Extension\Repositories\TestCase;

use Antares\Extension\Repositories\ComponentsRepository;

class ComponentsRepositoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var array
     */
    protected $branches = [
        'aaa' => '1.0',
        'bbb' => '1.1',
        'ccc' => '2.0',
    ];

    public function testGetBranches() {
        $repository = new ComponentsRepository($this->branches, [], []);

        $this->assertEquals($this->branches, $repository->getBranches());
    }

    public function testGetRequired() {
        $required = [
            'aaa',
            'ccc',
        ];

        $expected = [
            'aaa' => '1.0',
            'ccc' => '2.0',
        ];

        $repository = new ComponentsRepository($this->branches, $required, []);

        $this->assertEquals($expected, $repository->getRequired());
    }

    public function testGetRequiredWithNoBranch() {
        $required = [
            'aaa',
            'ddd',
        ];

        $expected = [
            'aaa' => '1.0',
            'ddd' => 'dev-master',
        ];

        $repository = new ComponentsRepository($this->branches, $required, []);

        $this->assertEquals($expected, $repository->getRequired());
    }

    public function testGetOptional() {
        $optional = [
            'aaa',
            'ccc',
        ];

        $expected = [
            'aaa' => '1.0',
            'ccc' => '2.0',
        ];

        $repository = new ComponentsRepository($this->branches, [], $optional);

        $this->assertEquals($expected, $repository->getOptional());
    }

    public function testGetOptionalWithNoBranch() {
        $optional = [
            'aaa',
            'ddd',
        ];

        $expected = [
            'aaa' => '1.0',
            'ddd' => 'dev-master',
        ];

        $repository = new ComponentsRepository($this->branches, [], $optional);

        $this->assertEquals($expected, $repository->getOptional());
    }

    public function testGetTargetBranch() {
        $repository = new ComponentsRepository($this->branches, [], []);

        $this->assertEquals('1.1', $repository->getTargetBranch('bbb'));
        $this->assertEquals('dev-master', $repository->getTargetBranch('eee'));
    }

    public function testGetWithBranches() {
        $repository = new ComponentsRepository($this->branches, [], []);

        $expected = [
            'aaa' => '1.0',
            'eee' => 'dev-master',
        ];

        $this->assertEquals($expected, $repository->getWithBranches(array_keys($expected)));
    }

}