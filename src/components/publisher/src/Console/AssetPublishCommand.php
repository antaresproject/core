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
use Symfony\Component\Console\Input\InputArgument;
use Antares\Publisher\Publishing\AssetPublisher;
use Antares\Publisher\Console\Traits\PublishingPathTrait;

class AssetPublishCommand extends Command
{
    use PublishingPathTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'publish:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a package's assets to the public directory";

    /**
     * The asset publisher instance.
     *
     * @var \Antares\Publisher\Publishing\AssetPublisher
     */
    protected $assets;

    /**
     * Create a new asset publish command instance.
     *
     * @param  \Antares\Publisher\Publishing\AssetPublisher  $assets
     */
    public function __construct(AssetPublisher $assets)
    {
        parent::__construct();

        $this->assets = $assets;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $package = $this->input->getArgument('package');

        $this->publishAssets($package);
    }

    /**
     * Publish the assets for a given package name.
     *
     * @param  string  $package
     *
     * @return void
     */
    protected function publishAssets($package)
    {
        if (! is_null($path = $this->getPath())) {
            $this->assets->publish($package, $path);
        } else {
            $this->assets->publishPackage($package);
        }

        $this->output->writeln('<info>Assets published for package:</info> '.$package);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The name of package being published.'],
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
            ['path', null, InputOption::VALUE_OPTIONAL, 'The path to the asset files.', null],
        ];
    }
}
