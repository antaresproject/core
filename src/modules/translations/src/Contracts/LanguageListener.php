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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Contracts;

interface LanguageListener
{

    /**
     * publish translations to files
     */
    public function publish($type);

    /**
     * response when publishing completed successfully
     */
    public function publishSucceed($type);

    /**
     * response when publishing failed
     */
    public function publishFailed($type, $error = null);

    /**
     * exporting translations
     */
    public function export($locale, $type);

    /**
     * response when exporting failed
     */
    public function exportFailed($error = null);

    /**
     * importing translations
     */
    public function import($locale, $type);

    /**
     * response when importing completed successfully
     */
    public function importSuccess($type, $locale);

    /**
     * response when exporting failed
     */
    public function importFailed();

    /**
     * change language
     */
    public function change($locale);

    /**
     * response when changing language completed successfully
     */
    public function changeSuccess();

    /**
     * response when changing language failed
     */
    public function changeFailed($error = null);
}
