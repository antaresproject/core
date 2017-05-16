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

use Antares\Logger\Contracts\DownloadPresenter as Presenter;
use Symfony\Component\HttpFoundation\File\File;
use Antares\Logger\Contracts\DownloadListener;
use Antares\Foundation\Processor\Processor;
use Urlcrypt\Urlcrypt;

class DownloadProcessor extends Processor
{

    protected $file;

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
     * default download action
     * 
     * @param String $path
     * @param DownloadListener $listener
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function download($path, DownloadListener $listener)
    {
        $decoded = Urlcrypt::decode($path);
        $file    = new File($decoded);
        if ($this->isImage($file)) {
            $path    = $file->getRealPath();
            $imgData = base64_encode(file_get_contents($path));
            $src     = 'data: ' . mime_content_type($path) . ';base64,' . $imgData;
            return $this->presenter->show($src);
        }
    }

    /**
     * verify whether file is an image
     * 
     * @param String $path
     * @return boolean
     */
    protected function isImage($file)
    {
        return in_array($file->getMimeType(), ['image/png', 'image/jpeg', 'image/jpg']);
    }

}
