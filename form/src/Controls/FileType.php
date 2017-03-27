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

    public function render()
    {
        $this->setOrientation('labelonly');
        $this->addAttribute('class', 'input-upload');
        if(!$this->label instanceof FileUploadLabel) {
            $oldLabel = clone $this->label;
            $this->label = new FileUploadLabel($oldLabel->name, $this);
        }

        $this->findErrors();
        $this->getLabel()->inputHtml = view('antares/foundation::form.controls.' . $this->type, ['control' => $this]);
        $this->getLabel()->controlName = $this->name;
        if(!$this->hasWrapper()) {
            $this->setWrapper(['class' => 'input-field']);
        }

        return view('antares/foundation::form.' . $this->orientation, [
            'label'   => $this->getLabel()->render(),
            'control' => $this,
            'errors'  => $this->messages['errors']?? [],
        ]);
    }

}
