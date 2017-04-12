<?php

declare(strict_types=1);

namespace Antares\Extension\Processors;

use Antares\Acl\Migration;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Model\Operation;
use Antares\Acl\RoleActionList;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Acl {

    /**
     * ACL migration instance.
     *
     * @var Migration
     */
    protected $migration;

    /**
     * Acl constructor.
     * @param Migration $migration
     */
    public function __construct(Migration $migration) {
        $this->migration = $migration;
    }

    /**
     * Import ACL of the given extension.
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param bool $reload
     */
    public function import(OperationHandlerContract $handler, ExtensionContract $extension, bool $reload = false) {
        $name = $extension->getPackage()->getName();

        try {
            $roleActionList = File::getRequire($extension->getPath() . '/acl.php');

            if($roleActionList instanceof RoleActionList) {
                if($reload) {
                    $this->migration->down($name);
                    $handler->operationInfo(new Operation('ACL settings have been flushed for ' . $name . '.'));
                }

                $handler->operationInfo(new Operation('Importing ACL settings for ' . $name . '.'));
                $this->migration->up($name, $roleActionList);
                $handler->operationInfo(new Operation('The ACL settings have been successfully imported.'));
            }
        }
        catch(FileNotFoundException $e) {
            $handler->operationInfo(new Operation('Skipping importing ACL settings for ' . $name . '.'));
            // No need to throw an exception because of ACL file can be optional. In that case the required file will be not found.
        }
        catch(\Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
