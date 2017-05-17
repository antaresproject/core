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
use Antares\Url\Contracts\Link as LinkContract;

class Link implements LinkContract {
    
    /**
     *
     * @var UrlContract
     */
    protected $url;
    
    /**
     * 
     * @param UrlContract $url
     */
    public function __construct(UrlContract $url) {
        $this->url = $url;
    }

    public function render() {
        $url    = $this->url->getUrl();
        $label  = $this->url->getLabel();
        
        return sprintf('<a href="%s">%s</a>', $url, $label);
    }

    public function isAuthorized() {
        return $this->url->isAuthorized();
    }

}
