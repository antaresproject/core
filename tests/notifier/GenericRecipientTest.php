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
 namespace Antares\Notifier\TestCase;

use Antares\Notifier\GenericRecipient;

class GenericRecipientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Antares\Notifier\GenericRecipient.
     *
     * @test
     */
    public function testGenericRecipient()
    {
        $email = 'admin@antaresplatform.com';
        $name  = 'Administrator';
        $stub  = new GenericRecipient($email, $name);

        $this->assertInstanceOf('\Antares\Contracts\Notification\Recipient', $stub);
        $this->assertEquals($email, $stub->getRecipientEmail());
        $this->assertEquals($name, $stub->getRecipientName());
    }
}
