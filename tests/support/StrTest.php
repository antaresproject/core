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

use Antares\Support\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test Authorize\Str::humanize() method.
     *
     * @test
     */
    public function testHumanizeMethod()
    {
        $expected = 'Foobar Is Awesome';
        $output   = Str::humanize('foobar-is-awesome');

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Authorize\Str::replace() method.
     *
     * @test
     */
    public function testReplaceMethod()
    {
        $expected = 'Antares Platform is awesome';
        $output   = Str::replace('{name} is awesome', ['name' => 'Antares Platform']);

        $this->assertEquals($expected, $output);

        $expected = [
            'Antares Platform is awesome',
            'Antares Platform is not a foobar',
        ];

        $data   = [
            '{name} is awesome',
            '{name} is not a {foo}',
        ];
        $output = Str::replace($data, ['name' => 'Antares Platform', 'foo' => 'foobar']);

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Authorize\Str::searchable() method.
     *
     * @test
     */
    public function testSearchableMethod()
    {
        $expected = ['foobar%'];
        $output   = Str::searchable('foobar*');

        $this->assertEquals($expected, $output);

        $expected = ['foobar', 'foobar%', '%foobar', '%foobar%'];
        $output   = Str::searchable('foobar');

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Support\Str::streamGetContents() method.
     *
     * @test
     */
    public function testStreamGetContentsMethod()
    {
        $string   = 'a:2:{s:4:"name";s:7:"Antares";s:5:"theme";a:2:{s:7:"backend";s:7:"default";s:8:"frontend";s:7:"default";}}';
        $stream   = fopen('data://text/plain;base64,' . base64_encode($string), 'r');
        $output   = Str::streamGetContents($stream);
        $expected = [
            'name'  => 'Antares',
            'theme' => [
                'backend'  => 'default',
                'frontend' => 'default',
            ],
        ];
        $this->assertEquals($expected, unserialize($output));
        $this->assertEquals('foo', Str::streamGetContents('foo'));
    }

    /**
     * Test the Antares\Support\Str::title method.
     *
     * @test
     */
    public function testStringCanBeConvertedToTitleCase()
    {
        $this->assertEquals('Taylor', Str::title('taylor'));
        $this->assertEquals('Άχιστη', Str::title('άχιστη'));
    }

}
