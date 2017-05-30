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
 namespace Antares\Publisher\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Antares\Publisher\Publishing\ViewPublisher;
use Symfony\Component\Console\Input\InputArgument;
use Antares\Publisher\Console\Traits\PublishingPathTrait;

class ViewPublishCommand extends Command
{
    use PublishingPathTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's views to the application";

    /**
     * The view publisher instance.
     *
     * @var \Antares\Publisher\Publishing\ViewPublisher
     */
    protected $view;

    /**
     * Create a new view publish command instance.
     *
     * @param  \Antares\Publisher\Publishing\ViewPublisher  $view
     */
    public function __construct(ViewPublisher $view)
    {
        parent::__construct();

        $this->view = $view;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $package = $this->input->getArgument('package');

        if (! is_null($path = $this->getPath())) {
            $this->view->publish($package, $path);
        } else {
            $this->view->publishPackage($package);
        }

        $this->output->writeln('<info>Views published for package:</info> '.$package);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The name of the package being published.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the source view files.', null],
        ];
    }
}
