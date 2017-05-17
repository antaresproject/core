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


namespace Antares\Asset;

class JavaScriptExpression
{

    /**
     * @var string the javascript expression wrapped by this object
     */
    public $code;

    /**
     * @param string $code a javascript expression that is to be wrapped by this object
     * @throws CException if argument is not a string
     */
    public function __construct($code)
    {
        if (!is_string($code)) {
            throw new \Exception('Value passed to CJavaScriptExpression should be a string.');
        }
        if (strpos($code, 'js:') === 0) {
            $code = substr($code, 3);
        }
        $this->code = $code;
    }

    /**
     * String magic method
     * 
     * @return string the javascript expression wrapped by this object
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * renders javascript code
     * 
     * @return string the javascript expression wrapped by this object
     */
    public function render()
    {
        return $this->code;
    }

}
