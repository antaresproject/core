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

use Arcanedev\LogViewer\Contracts\LogStylerInterface;
use Illuminate\Contracts\Config\Repository;
use Antares\Logger\Entities\Log;

class LogMenu
{

    /**
     * The config repository instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * The log styler instance.
     *
     * @var \Arcanedev\LogViewer\Contracts\LogStylerInterface
     */
    private $styler;

    /* ------------------------------------------------------------------------------------------------
      |  Constructor
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Create the LogMenu instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository            $config
     * @param  \Arcanedev\LogViewer\Contracts\LogStylerInterface  $styler
     */
    public function __construct(Repository $config, LogStylerInterface $styler)
    {
        $this->setConfig($config);
        $this->setLogStyler($styler);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Getters & Setters
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Set the config instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     *
     * @return self
     */
    public function setConfig(Repository $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Set the log styler instance.
     *
     * @param  \Arcanedev\LogViewer\Contracts\LogStylerInterface  $styler
     *
     * @return self
     */
    public function setLogStyler(LogStylerInterface $styler)
    {
        $this->styler = $styler;

        return $this;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Main Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Make log menu.
     *
     * @param  \Arcanedev\LogViewer\Entities\Log  $log
     * @param  bool                               $trans
     *
     * @return array
     */
    public function make(Log $log, $trans = true)
    {
        $items = [];
        $route = config('antares/logger::menu.filter-route');

        foreach ($log->tree($trans) as $level => $item) {
            $items[$level] = array_merge($item, [
                'url'  => route($route, [$log->date, $level]),
                'icon' => $this->isIconsEnabled() ? $this->styler->icon($level) : '',
            ]);
        }

        return $items;
    }

    /* ------------------------------------------------------------------------------------------------
      |  Check Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Check if the icons are enabled.
     *
     * @return bool
     */
    private function isIconsEnabled()
    {
        return (bool) $this->config('menu.icons-enabled', false);
    }

    /* ------------------------------------------------------------------------------------------------
      |  Other Functions
      | ------------------------------------------------------------------------------------------------
     */

    /**
     * Get config.
     *
     * @param  string      $key
     * @param  mixed|null  $default
     *
     * @return mixed
     */
    private function config($key, $default = null)
    {
        return $this->config->get('log-viewer.' . $key, $default);
    }

}
