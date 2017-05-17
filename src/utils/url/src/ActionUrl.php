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

use Antares\Url\Permissions\CanHandler;

class ActionUrl extends AbstractUrl {
    
    /**
     *
     * @var string
     */
    protected $url;
    
    /**
     *
     * @var label
     */
    protected $label;
    
    public function __construct(CanHandler $canHandler, $url, $label, $aclAction = null) {
        parent::__construct($canHandler, $aclAction);
        
        $this->url      = $url;
        $this->label    = $label;
    }
    
    public function getUrl() {
        return $this->url;
    }

    public function getLabel() {
        return $this->label;
    }

}
