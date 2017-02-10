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


namespace Antares\Extension\Processor;

use Illuminate\Support\Fluent;
use Antares\Contracts\Extension\Factory;
use Antares\Contracts\Extension\Listener\Migrator as Listener;
use Illuminate\Support\Facades\DB;
use Antares\Support\Facades\Foundation;
use Illuminate\Support\Facades\Log;

class Delete extends Processor
{

    /**
     * deletes an extension.
     * @param  \Antares\Contracts\Extension\Listener\Migrator  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     * @return mixed
     */
    public function delete(Listener $listener, Uninstaller $uninstaller, Fluent $extension)
    {
        DB::beginTransaction();
        $exception = false;
        try {
            $uninstalled = $uninstaller->uninstall($listener, $extension, false);
            if (!$uninstalled) {
                throw new \Exception('Unable to uninstall extension. Deletion process has been stopped.');
            }
            $name      = $extension->get('name');
            call_user_func($this->getDeleteClosure(), $this->factory, $name);
            $component = Foundation::make('antares.component');
            $package   = $component::findOneByName($extension->get('name'));
            if (!is_null($package)) {
                $package->delete();
            }
        } catch (\Exception $ex) {
            Log::emergency($ex);
            $exception = $ex;
        }
        if (!$exception) {
            DB::commit();
            return $listener->deleteHasSucceed($extension);
        } else {
            DB::rollback();
            return $listener->deleteHasFailed($extension, ['error' => $exception->getMessage()]);
        }
    }

    /**
     * Get deletion closure.
     * @return callable
     */
    protected function getDeleteClosure()
    {
        return function (Factory $factory, $name) {
            $factory->delete($name);
        };
    }

}
