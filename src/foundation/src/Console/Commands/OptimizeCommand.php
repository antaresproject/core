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


namespace Antares\Foundation\Console\Commands;

use RuntimeException;
use ClassPreloader\Factory;
use ClassPreloader\Exceptions\SkipFileException;
use ClassPreloader\Exceptions\VisitorExceptionInterface;
use Illuminate\Foundation\Console\OptimizeCommand as Command;

class OptimizeCommand extends Command
{

    /**
     * Generate the compiled class file.
     *
     * @return void
     */
    protected function compileClasses()
    {
        $preloader = (new Factory())->create(['skip' => true]);

        $path = $this->laravel->getCachedCompilePath();

        if (file_exists($path)) {
            unlink($path);
        }

        $handle = $preloader->prepareOutput($path . '.tmp');

        foreach ($this->getClassFiles() as $file) {
            try {
                fwrite($handle, $preloader->getCode($file, false) . "\n");
            } catch (SkipFileException $ex) {
                // Class Preloader 2.x
            } catch (VisitorExceptionInterface $e) {
                // Class Preloader 3.x
            } catch (RuntimeException $e) {
                // Handle when fwrite fails.
            }
        }

        fclose($handle);

        rename($path . '.tmp', $path);
    }

}
