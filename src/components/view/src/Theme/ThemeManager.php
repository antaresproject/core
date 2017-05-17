<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\View\Theme;

use Illuminate\Support\Manager;

class ThemeManager extends Manager
{

    /**
     * Create an instance of the antares theme driver.
     *
     * @return \Antares\Contracts\Theme\Theme
     */
    protected function createAntaresDriver()
    {
        $theme = new Theme($this->app, $this->app->make('events'), $this->app->make('files'));

        return $theme->initiate();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDriver()
    {
        return 'antares';
    }

    /**
     * Detect available themes.
     *
     * @return array
     */
    public function detect()
    {
        return $this->app->make('antares.theme.finder')->detect();
    }

}
