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


namespace Antares\Users\Processor;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Antares\Users\Validation\PictureValidator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

class ProfilePicture extends User
{

    /**
     * Uploads profile picture
     * 
     * @return Response
     */
    public function picture()
    {
        $uploadedFile            = Input::all()['file'];
        $file                    = $this->resolveTempFileName($uploadedFile);
        $uploadedFile->directory = $file['directory'];
        $uploadedFile->filename  = $file['filename'];

        $validation = app(PictureValidator::class)->on('upload')->with(['file' => $uploadedFile]);
        if ($validation->fails()) {
            return Response::make($validation->getMessageBag()->first(), 400);
        }

        return ($uploadedFile->move($uploadedFile->directory, $uploadedFile->filename)) ?
                Response::json(['path' => $uploadedFile->directory . DIRECTORY_SEPARATOR . $uploadedFile->filename], 200) :
                Response::make(trans('Unable to upload file.'), 400);
    }

    /**
     * Uploads file to temporary folder
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return array
     */
    protected function resolveTempFileName(UploadedFile $file)
    {
        $name      = $file->getClientOriginalName();
        $extension = File::extension($name);
        $subdir    = sha1(time());
        $directory = storage_path() . '/app/uploads/' . $subdir;
        $filename  = sha1(time() . time()) . ".{$extension}";
        return ['directory' => $directory, 'subdir' => $subdir, 'filename' => $filename];
    }

    /**
     * Sets profile picture as gravatar
     * 
     * @return boolean
     */
    public function gravatar()
    {
        $metas = auth()->user()->meta->filter(function($item) {
            return $item->name == 'picture';
        });
        if ($metas->count() > 0) {
            return $metas->first()->delete();
        }
        return false;
    }

}
