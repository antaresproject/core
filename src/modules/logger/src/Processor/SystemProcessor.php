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

namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\SystemPresenter as Presenter;
use Antares\Foundation\Processor\Processor;

class SystemProcessor extends Processor
{

    /**
     * constructing
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * default index action
     * 
     * @return type
     */
    public function index()
    {
        publish('logger', ['/css/theme_default.css']);
        return $this->presenter->index();
    }

}
