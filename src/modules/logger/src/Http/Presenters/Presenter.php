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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Http\Presenters;

use Antares\Foundation\Http\Presenters\Presenter as SupportPresenter;

class Presenter extends SupportPresenter
{

    /**
     * acl instance
     *
     * @var \Antares\Authorization\Authorization 
     */
    protected static $acl;

    /**
     * get instance of acl object or default getter
     * 
     * @param String $name
     * @return boolean|mixed
     */
    public function __get($name)
    {
        if ($name == 'acl') {
            if (is_null(self::$acl)) {
                $namespace = app('antares.extension')->finder()->resolveNamespace(__FILE__);
                self::$acl = app('antares.acl')->make($namespace);
            }
            return self::$acl;
        }
        return parent::__get($name);
    }

    /**
     * custom acl caller
     * 
     * @param String $name
     * @param String $arguments
     * @return boolean|mixed
     */
    public function __call($name, $arguments)
    {
        if ($name == 'can') {
            return $this->acl->can($arguments[0]);
        }
        return parent::__call($name, $arguments);
    }

}
