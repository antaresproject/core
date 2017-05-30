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
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Antares\Publisher\Publishing\ConfigPublisher;
use Antares\Publisher\Console\Traits\PublishingPathTrait;

class ConfigPublishCommand extends Command
{
    use ConfirmableTrait, PublishingPathTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's configuration to the application";

    /**
     * The config publisher instance.
     *
     * @var \Antares\Publisher\Publishing\ConfigPublisher
     */
    protected $config;

    /**
     * Create a new configuration publish command instance.
     *
     * @param  \Antares\Publisher\Publishing\ConfigPublisher  $config
     */
    public function __construct(ConfigPublisher $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $package = $this->input->getArgument('package');

        $proceed = $this->confirmToProceed('Config Already Published!', function () use ($package) {
            return $this->config->alreadyPublished($package);
        });

        if (! $proceed) {
            return;
        }

        if (! is_null($path = $this->getPath())) {
            $this->config->publish($package, $path);
        } else {
            $this->config->publishPackage($package);
        }

        $this->output->writeln('<info>Configuration published for package:</info> '.$package);
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
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the configuration files.', null],

            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the file already exists.'],
        ];
    }
}
