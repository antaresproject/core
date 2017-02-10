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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 namespace Antares\Extension\Console;

class DetectCommand extends BaseCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'extension:detect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect available extensions in the application.';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $service    = $this->laravel['antares.extension'];
        $extensions = $service->detect();

        if (empty($extensions)) {
            return $this->line('<comment>No extension detected!</comment>');
        }

        $header  = ['Extension', 'Version', 'Activate'];
        $content = [];

        foreach ($extensions as $name => $options) {
            $content[] = [
                $name,
                $options['version'],
                $service->started($name) ? '    ✓' : '',
            ];
        }

        $this->table($header, $content);
    }
}
