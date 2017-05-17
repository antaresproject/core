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


namespace Antares\Url\Permissions;

use Antares\Contracts\Foundation\Foundation;

class CanHandler {
    
    /**
     *
     * @var Foundation 
     */
    protected $foundation;
    
    /**
     * module action delimiter
     *
     * @var string
     */
    private static $delimiter = '::';
    
    /**
     * 
     * @param Foundation $foundation
     */
    public function __construct(Foundation $foundation) {
        $this->foundation   = $foundation;
    }
    
    /**
     * 
     * @param string $action
     * @return boolean
     */
    public function canAuthorize($action) {
        if (empty($action)) {
            return false;
        }
        
        if (str_contains($action, self::$delimiter)) {
            list($module, $action) = explode(self::$delimiter, $action);
            
            return $this->foundation->make('antares.acl')->make($module)->can($action);
        }
        
        return $this->foundation->acl()->can($action);
    }
    
}
