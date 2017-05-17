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


namespace Antares\Url;

use Antares\Url\Contracts\Url as UrlContract;
use Antares\Url\Permissions\CanHandler;

abstract class AbstractUrl implements UrlContract {
    
    /**
     *
     * @var CanHandler 
     */
    protected $canHandler;
    
    /**
     *
     * @var string | null
     */
    protected $aclAction;
    
    /**
     * 
     * @param CanHandler $canHandler
     * @param string | null $aclAction
     */
    public function __construct(CanHandler $canHandler, $aclAction = null) {
        $this->canHandler   = $canHandler;
        $this->aclAction    = $aclAction;
    }
    
    /**
     * 
     * @return bool
     */
    public function isAuthorized() {
        return $this->canHandler->canAuthorize($this->aclAction);
    }
    
}
