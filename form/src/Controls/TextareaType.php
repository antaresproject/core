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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Controls;

class TextareaType extends AbstractType
{
    
    protected $type = 'textarea';
    
    /**
     * @param int $rows
     * @return AbstractType
     */
    public function setRows(int $rows) : AbstractType
    {
        return $this->setAttribute('rows', $rows);
    }
    
    
    
}
