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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Installer\Validator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\Validator;
use function app;
use function array_get;
use function storage_path;

class LicenseValidator extends Validator
{

    /**
     * valid certificate file extensions
     *
     * @var array
     */
    protected $extensions = ['key', 'cert'];

    /**
     * validate certificate file type
     * 
     * @param String $attribute
     * @param array $parameters
     */
    public function validateCertificate($attribute, $parameters)
    {
        $filesystem = app(Filesystem::class);
        $files      = $filesystem->allFiles(storage_path('license'));
        if (empty($files)) {
            $this->messages->add($attribute, $this->translator->trans('Certification files has not been saved. Please try again.'));
            return false;
        }
        foreach ($files as $file) {
            if (!in_array($file->getExtension(), $this->extensions)) {
                $this->messages->add($attribute, $this->translator->trans('Invalid certificate file. Only type of *.cert are accepted.'));
                return false;
            }
        }
        $validated = app('antares.license')->validate();
        $result    = array_get($validated, 'RESULT');
        if ($result !== 'OK') {
            $this->messages->add($attribute, $this->translator->trans("antares/licensing::global.{$result}"));
            return false;
        }
        return true;
    }

}
