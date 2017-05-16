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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Http\Filters;

use Antares\Contracts\Foundation\Foundation;
use Antares\Contracts\Authorization\Factory;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use Antares\Customfields\Exception;

class CanManage
{

    /**
     * application foundation
     * 
     * @var Foundation
     */
    protected $foundation;

    /**
     * authorization factory implementation
     * 
     * @var \Antares\Contracts\Authorization\Authorization
     */
    protected $acl;

    /**
     * constructor
     * @param Foundation $foundation
     * @param Factory $acl
     */
    public function __construct(Foundation $foundation, Factory $acl)
    {
        $this->foundation = $foundation;
        $this->acl        = $acl->make('antares/tester');
    }

    /**
     * checks user is allowed to get the action
     * 
     * @param Route $router
     * @param Request $request
     * @param String $value
     */
    public function filter(Route $router, Request $request, $value = '')
    {
        if (is_null($value) OR strlen($value) == 0 OR strpos($value, '-') === false) {
            throw new Exception\InvalidArgumentException('Invalid argument exception.');
        }

        list($action, $type) = explode('-', $value);
        if (!$this->checkUserAuthorization($action, $type)) {
            $message = trans('antares/foundation::response.acl.not-allowed');
            return redirect_with_message(handles('admin'), $message);
        }
    }

    /**
     * Can the user take this action.
     * @param  string  $action
     * @param  string  $type
     * @return bool
     */
    protected function checkUserAuthorization($action, $type)
    {
        $acl = $this->acl;
        return ($acl->can("{$action} {$type}"));
    }

}
