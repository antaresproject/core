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


namespace Antares\Url\Decorators;

use Antares\Url\Contracts\Link as LinkContract;

class DisappearanceLink implements LinkContract {
    
    /**
     *
     * @var LinkContract
     */
    protected $link;
    
    public function __construct(LinkContract $link) {
        $this->link = $link;
    }

    public function isAuthorized() {
        return $this->link->isAuthorized();
    }

    public function render() {
        return $this->link->isAuthorized()
                ? $this->link->render()
                : '';
    }

}
