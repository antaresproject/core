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



namespace Antares\Logger\Utilities;

use Illuminate\Translation\Translator;
use Psr\Log\LogLevel;
use ReflectionClass;

class LogLevels
{
    /* ------------------------------------------------------------------------------------------------
      |  Properties
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * The log levels.
     *
     * @var array
     */
    protected static $levels = [];

    /**
     * @var Translator
     */
    private $translator;

    /* ------------------------------------------------------------------------------------------------
      |  Constructor
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Create LogLevels instance.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get the log levels.
     *
     * @param  bool|false  $flip
     *
     * @return array
     */
    public function lists($flip = false)
    {
        return self::all($flip);
    }

    /**
     * Get translated levels.
     *
     * @param  string|null  $locale
     *
     * @return array
     */
    public function names($locale = null)
    {
        $levels = self::all(true);

        array_walk($levels, function (&$name, $level) use ($locale) {
            $name = $this->getTranslatedName($level, $locale);
        });

        return $levels;
    }

    /**
     * Get PSR log levels.
     *
     * @param  bool|false  $flip
     *
     * @return array
     */
    public static function all($flip = false)
    {
        if (empty(self::$levels)) {
            $class        = new ReflectionClass(new LogLevel);
            self::$levels = $class->getConstants();
        }

        return $flip ? array_flip(self::$levels) : self::$levels;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Translate a level.
     *
     * @param  string       $level
     * @param  string|null  $locale
     *
     * @return string
     */
    private function getTranslatedName($level, $locale)
    {
        if ($locale === 'auto') {
            $locale = null;
        }

        return $this->translator->get('log-viewer::levels.' . $level, [], $locale);
    }

}
