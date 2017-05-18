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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Labels;


/**
 * @author Marcin Domański <marcin@domanskim.pl>
 * Date: 24.03.17
 * Time: 14:06
 */
class FileUploadLabel extends AbstractLabel
{

    public $type = 'fileupload';
    public $inputHtml;
    public $controlName;

    public function render()
    {
        $this->setAttributes(['class' => 'file-upload']);
        return view('antares/foundation::form.labels.' . $this->type,
            ['label' => $this, 'controlName' => $this->controlName, 'controlHtml' => $this->inputHtml])->render();
    }

}