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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Brands\Processor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;

class Logo
{

    public function upload($input)
    {
        $key    = key($input);
        $config = config('antares/brands::logo');
        $rules  = array_get($config, 'rules');

        $mimes       = implode(',', array_get($rules, 'acceptedFiles'));
        $maxFileSize = array_get($rules, 'maxFilesize');
        $minFileSize = array_get($rules, 'minFilesize');

        $validator = Validator::make($input, [$key => ['required', 'mimes:' . $mimes, 'min:' . $minFileSize]], [
                    'mimes'    => trans('antares/brands::validation.mimes', ['extensions' => $mimes]),
                    'max.file' => trans('antares/brands::validation.max_file_size', ['size' => $maxFileSize]),
        ]);
        $this->validateImageDimensions($validator, $input, $rules, $key);
        if ($validator->fails()) {
            return Response::make($validator->getMessageBag()->first(), 400);
        }
        $file        = $input[$key];
        $name        = str_random(40);
        $filename    = $name . '.' . $file->extension();
        $destination = array_get($config, 'destination');
        if (!$file->move($destination, $filename)) {
            return Response::make('antares/brands::response.upload.error', 400);
        }
        return Response::json(['path' => $filename], 200);
    }

    /**
     * validates image dimensions
     * 
     * @param \Illuminate\Validation\Validator $validator
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $rules
     * @param String $key
     * @return boolean
     */
    protected function validateImageDimensions(&$validator, $file, $rules, $key)
    {
        $validator->after(function($validator) use($file, $rules, $key) {
            $path       = $file[$key]->getRealPath();
            $size       = getimagesize($path);
            $keyname    = $key == 'logo' ? 'main' : $key;
            $dimensions = array_get($rules, 'dimensions.' . $keyname);
            if (isset($dimensions['max_width']) and $size[0] > $dimensions['max_width']) {
                $validator->errors()->add('image', trans('antares/brands::validation.max_width', ['width' => $dimensions['max_width']]));
            }
            if (isset($dimensions['min_width']) and $size[0] < $dimensions['min_width']) {
                $validator->errors()->add('image', trans('antares/brands::validation.min_width', ['width' => $dimensions['min_width']]));
            }
            if (isset($dimensions['max_height']) and $size[1] > $dimensions['max_height']) {
                $validator->errors()->add('image', trans('antares/brands::validation.max_height', ['height' => $dimensions['max_height']]));
            }
            if (isset($dimensions['min_height']) and $size[1] < $dimensions['min_height']) {
                $validator->errors()->add('image', trans('antares/brands::validation.min_height', ['height' => $dimensions['min_height']]));
            }
        });
        return true;
    }

}
