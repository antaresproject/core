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



use Antares\Asset\JavaScriptExpression;

class JavaScriptDecorator
{

    /**
     * quoting javascript inline expressions
     * @param String $js
     * @param boolean $forUrl
     * @return String
     */
    public static function quote($js, $forUrl = false)
    {
        if ($forUrl)
            return strtr($js, array('%' => '%25', "\t" => '\t', "\n" => '\n', "\r" => '\r', '"' => '\"', '\'' => '\\\'', '\\' => '\\\\', '</' => '<\/'));
        else
            return strtr($js, array("\t" => '\t', "\n" => '\n', "\r" => '\r', '"' => '\"', '\'' => '\\\'', '\\' => '\\\\', '</' => '<\/'));
    }

    /**
     * decorating javascript inline expression 
     * @param \Antares\Asset\JavaScriptExpression $value
     * @param boolean $safe
     * @return string
     */
    public static function decorate($value, $safe = false)
    {
        if (is_string($value)) {
            if (strpos($value, 'js:') === 0 && $safe === false)
                return substr($value, 3);
            else
                return "'" . self::quote($value) . "'";
        }
        elseif ($value === null)
            return 'null';
        elseif (is_bool($value))
            return $value ? 'true' : 'false';
        elseif (is_integer($value))
            return "$value";
        elseif (is_float($value)) {
            if ($value === -INF)
                return 'Number.NEGATIVE_INFINITY';
            elseif ($value === INF)
                return 'Number.POSITIVE_INFINITY';
            else
                return str_replace(',', '.', (float) $value);          }
        elseif ($value instanceof JavaScriptExpression)
            return $value->__toString();
        elseif (is_object($value))
            return self::decorate(get_object_vars($value), $safe);
        elseif (is_array($value)) {
            $es = array();
            if (($n  = count($value)) > 0 && array_keys($value) !== range(0, $n - 1)) {
                foreach ($value as $k => $v)
                    $es[] = "'" . self::quote($k) . "':" . self::decorate($v, $safe);
                return '{' . implode(',', $es) . '}';
            } else {
                foreach ($value as $v)
                    $es[] = self::decorate($v, $safe);
                return '[' . implode(',', $es) . ']';
            }
        } else
            return '';
    }

}
