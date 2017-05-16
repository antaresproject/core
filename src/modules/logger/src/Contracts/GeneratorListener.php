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



namespace Antares\Logger\Contracts;

interface GeneratorListener
{

    /**
     * generate report base on input data
     */
    public function generate();

    /**
     * when report generation failed
     */
    public function generateFailed();

    /**
     * response when generates standalone system report
     */
    public function generateStandalone();

    /**
     * when downloads report by filename
     */
    public function downloadReport($filename);
}
