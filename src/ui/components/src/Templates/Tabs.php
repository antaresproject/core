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

class Tabs extends AbstractTemplate
{

    /**
     * name of template used by ui component
     * 
     * @var String
     */
    protected $template = 'tabs';

    /**
     * Tabs definition
     *
     * @var array
     */
    protected $tabs = [];

    /**
     * Ui component attributes
     * 
     * @var array
     */
    protected $attributes = [
        'titlable' => false,
        'editable' => false,
        'nestable' => false,
    ];

    /**
     * {@inherited}
     */
    public function render()
    {
        
    }

    /**
     * {@inherited}
     */
    public function __toString()
    {
        foreach ($this->tabs as &$tab) {
            if (!class_exists($tab['component'])) {
                continue;
            }

            $instance           = !is_object($tab['component']) ? app($tab['component']) : $tab['component'];
            $instance->tabbable = true;
            $tab['component']   = $instance->render();
        }
        $this->attributes['tabs'] = $this->tabs;
        return parent::__toString();
    }

}
