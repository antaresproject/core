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



namespace Antares\Logger\Adapter;

use Illuminate\Filesystem\Filesystem;

class ChecksumAdapter
{

    /**
     * instance of filesystem
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * constructing
     * 
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * verify application files checksum
     * 
     * @return array 
     */
    public function verify()
    {
        $files     = $this->filesystem->allFiles(base_path('src'));
        $memory    = app('antares.memory')->make('checksum');
        $checksums = $memory->all();
        $updates   = [];
        $allFiles  = [];
        foreach ($files as $file) {
            $key            = $file->getRelativePath() . DIRECTORY_SEPARATOR . $file->getFilename();
            $checksum       = md5_file($file->getRealPath());
            $allFiles[$key] = $checksum;
            if (isset($checksums[$key]) && $checksums[$key] !== $checksum) {
                $updates[$key] = $checksum;
            }
        }
        $inserts = array_diff(array_keys($allFiles), array_keys($checksums));
        $deletes = array_diff(array_keys($checksums), array_keys($allFiles));

        $return = ['inserts' => $inserts, 'updates' => $updates, 'deletes' => $deletes];
        $this->validate($return);
        return $return;
    }

    /**
     * validate checksum verification
     * 
     * @param array $data
     * @return array
     */
    protected function validate(array &$data)
    {
        $messages = [];
        if (isset($data['inserts']) && !empty($data['inserts'])) {
            array_push($messages, ['warning', sprintf('Found %s new files. New, not original files may increase the risk of instability.', count($data['inserts']))]);
        }
        if (isset($data['deletes']) && !empty($data['deletes'])) {
            array_push($messages, ['warning', sprintf('%s files has been deleted. Deleting system files may increase the risk of instability.', count($data['deletes']))]);
        }
        if (isset($data['updates']) && !empty($data['updates'])) {
            array_push($messages, ['warning', sprintf('%s files has been changed. File modifications are unacceptable and can increase the risk of instability.', count($data['updates']))]);
        }
        $data = array_merge($data, ['messages' => $messages]);
        return $data;
    }

}
