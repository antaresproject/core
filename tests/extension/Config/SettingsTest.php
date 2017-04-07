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

namespace Antares\Extension\Settings\TestCase;

use Antares\Extension\Config\Settings;
use Antares\Extension\Contracts\Config\SettingsContract;

class SettingsTest extends \PHPUnit_Framework_TestCase
{

    public function testContract() {
        $this->assertInstanceOf(SettingsContract::class, new Settings());
    }

    public function testHasDataMethodWithEmptyData() {
        $settings = new Settings();

        $this->assertFalse($settings->hasData());
    }

    public function testGetDataMethod() {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $settings = new Settings($data);

        $this->assertEquals('foo', $settings->getValueByName('a'));
        $this->assertEquals('bar', $settings->getValueByName('b'));
        $this->assertNull($settings->getValueByName('c'));
        $this->assertEquals('default-value', $settings->getValueByName('d', 'default-value'));
    }

    public function testUpdateDataMethod() {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $newData = [
            'b' => 'bar2',
            'c' => 'baz',
        ];

        $expected = [
            'a' => 'foo',
            'b' => 'bar2',
            'c' => 'baz',
        ];

        $settings = new Settings($data);
        $settings->updateData($newData);

        $this->assertEquals($expected, $settings->getData());
    }

    public function testGetValueByNameMethod() {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $settings = new Settings($data);

        $this->assertEquals($data, $settings->getData());
        $this->assertTrue($settings->hasData());
    }

    public function testGetValidationRulesMethod() {
        $rules = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $settings = new Settings([], $rules);

        $this->assertEquals($rules, $settings->getValidationRules());
    }

    public function testGetValidationPhrasesMethod() {
        $phrases = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $settings = new Settings([], [], $phrases);

        $this->assertEquals($phrases, $settings->getValidationPhrases());
    }

    public function testToArrayMethod() {
        $data = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $rules = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $phrases = [
            'a' => 'foo',
            'b' => 'bar',
        ];

        $expected = compact('data', 'rules', 'phrases');

        $settings = new Settings($data, $rules, $phrases);

        $this->assertEquals($expected, $settings->toArray());
    }

}
