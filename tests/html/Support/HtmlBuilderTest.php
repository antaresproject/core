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

namespace Antares\Html\Support\TestCase;

use Mockery as m;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Antares\Html\Support\HtmlBuilder;

class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Antares\Html\Support\HtmlBuilder
     */
    protected $html;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $generator  = new UrlGenerator(new RouteCollection(), Request::create('/foo', 'GET'));
        $this->html = new HtmlBuilder($generator);
    }

    public function testDl()
    {
        $list = [
            'foo'  => 'bar',
            'bing' => 'baz',
        ];

        $attributes = ['class' => 'example'];

        $result = $this->html->dl($list, $attributes);

        $this->assertEquals('<dl class="example"><dt>foo</dt><dd>bar</dd><dt>bing</dt><dd>baz</dd></dl>', $result);
    }

    public function testMeta()
    {
        $result = $this->html->meta('description', 'Lorem ipsum dolor sit amet.');

        $this->assertEquals('<meta name="description" content="Lorem ipsum dolor sit amet.">' . PHP_EOL, $result);
    }

    public function testMetaOpenGraph()
    {
        $result = $this->html->meta(null, 'website', ['property' => 'og:type']);

        $this->assertEquals('<meta content="website" property="og:type">' . PHP_EOL, $result);
    }

}
