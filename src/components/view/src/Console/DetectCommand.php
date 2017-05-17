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


namespace Antares\View\Console;

use Illuminate\Console\Command as IlluminateCommand;
use Antares\View\Theme\Finder;
use Antares\View\Theme\Manifest;

class DetectCommand extends IlluminateCommand
{

    /**
     * Theme finder instance.
     *
     * @var \Antares\View\Theme\Finder
     */
    protected $finder;

    /**
     * human readable command name
     *
     * @var String
     */
    protected $title = 'Theme detector';

    /**
     * when command should be executed
     *
     * @var String
     */
    protected $launched = [
        'twiceDaily' => [1, 13]
    ];

    /**
     * when command can be executed
     *
     * @var array
     */
    protected $availableLaunches = [
        'everyFiveMinutes',
        'everyTenMinutes',
        'everyThirtyMinutes',
        'hourly',
        'daily'
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:detect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect available themes in the application.';

    /**
     * Construct a new status command.
     *
     * @param  \Antares\View\Theme\Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $memory = $this->laravel['antares.memory'];

        $themes   = $this->finder->detect();
        $frontend = $memory->get('site.theme.frontend');
        $backend  = $memory->get('site.theme.backend');

        $header  = ['ID', 'Theme Name', 'Frontend', 'Backend'];
        $content = [];

        foreach ($themes as $id => $theme) {
            $content[] = [
                $id,
                $theme->get('name'),
                $this->getThemeStatus('frontend', $theme, ($id == $frontend)),
                $this->getThemeStatus('backend', $theme, ($id == $backend)),
            ];
        }

        $this->table($header, $content);
    }

    /**
     * Get theme status.
     *
     * @param  string  $type
     * @param  \Antares\View\Theme\Manifest  $theme
     * @param  bool  $active
     *
     * @return string
     */
    protected function getThemeStatus($type, Manifest $theme, $active = false)
    {
        if ($active === true) {
            return '   ✓';
        }

        $group = $theme->get('type');

        if (!empty($group) && !in_array($type, $group)) {
            return '   ✗';
        }

        return '';
    }

}
