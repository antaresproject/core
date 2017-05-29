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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Templates;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;

class DefaultTemplate extends AbstractTemplate
{

    /**
     * Name of template used by ui component
     * 
     * @var String
     */
    protected $template = 'empty';

    /**
     * Renders widget content
     */
    public function render()
    {
        
    }

}
