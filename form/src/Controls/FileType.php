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

use Antares\Form\Labels\FileUploadLabel;

class FileType extends AbstractType
{
    
    /** @var string */
    protected $type = 'file';

    public $width = '100%';

    public function render()
    {
        //$this->setOrientation('labelonly');
        $this->addAttribute('class', $this->name);
        $this->addAttribute('id', $this->name);
        if(!$this->hasWrapper()) {
            $this->setWrapper(['class' => 'input-field']);
        }

        return parent::render();
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function setWidth(string $width)
    {
        $this->width = $width;
        return $this;
    }

}
