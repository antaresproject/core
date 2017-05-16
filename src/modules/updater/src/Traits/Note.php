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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Traits;

trait Note
{

    /**
     * notes container
     *
     * @var array
     */
    protected $notes = [];

    /**
     * has error flag
     *
     * @var boolean
     */
    protected $hasError = false;

    /**
     * Raise a note event for the migrator.
     *
     * @param  string  $message
     * @return void
     */
    protected function note($message)
    {
        $this->notes[] = $message;
    }

    /**
     * Get the notes for the last operation.
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * clear notes
     * 
     * @return mixed
     */
    public function clearNotes()
    {
        $this->notes = [];
        return $this;
    }

    /**
     * hasError getter
     * 
     * @return boolean
     */
    public function hasError()
    {
        return $this->hasError;
    }

}
