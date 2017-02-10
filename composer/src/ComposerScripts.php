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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Composer;

use Symfony\Component\Process\Process;
use Illuminate\Filesystem\Filesystem;
use Composer\Script\Event;

class ComposerScripts
{

    /**
     * Handle the pre-update or pre-install Composer event.
     *
     * @param  \Composer\Script\Event  $event
     *
     * @return void
     */
    public static function preUpdate(Event $event)
    {
        $path     = realpath($event->getComposer()->getConfig()->get('vendor-dir') . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'components');
        $commands = self::getComposerCommands($path);

        foreach ($commands as $command) {
            $process = new Process($command);
            $process->run();
            if (!$process->isSuccessful()) {
                echo $process->getErrorOutput();
            }
            echo $process->getErrorOutput();
        }

    }

    /**
     * get composer commands
     * 
     * @param String $path
     * @return array
     */
    protected static function getComposerCommands($path)
    {
        $filesystem  = new Filesystem();
        $directories = $filesystem->directories($path);
        $commands    = [];
        foreach ($directories as $directory) {
            $composer = $directory . DIRECTORY_SEPARATOR . 'composer.json';
            if (!$filesystem->exists($composer)) {
                continue;
            }
            $operation = !$filesystem->exists($directory . DIRECTORY_SEPARATOR . 'composer.lock') ? 'install' : 'update';
            $command   = "composer {$operation} -d " . dirname($composer);
            array_push($commands, $command);
        }
        return $commands;
    }

}
