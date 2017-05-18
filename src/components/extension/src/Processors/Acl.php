<?php

declare(strict_types = 1);

namespace Antares\Extension\Processors;

use Antares\Acl\Migration;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Contracts\Handlers\OperationHandlerContract;
use Antares\Extension\Model\Operation;
use Antares\Acl\RoleActionList;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Acl
{

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
    public function __construct(Migration $migration)
    {
        $this->migration = $migration;
    }

    /**
     * Import ACL of the given extension.
     *
     * @param OperationHandlerContract $handler
     * @param ExtensionContract $extension
     * @param bool $reload
     */
    public function import(OperationHandlerContract $handler, ExtensionContract $extension, bool $reload = false)
    {
        $name     = $extension->getPackage()->getName();
        $filePath = $extension->getPath() . '/acl.php';
        if (!File::exists($filePath)) {
            $handler->operationInfo(new Operation('Skipping importing ACL settings for ' . $name . '.'));
            return;
        }

        try {
            $roleActionList = File::getRequire($filePath);

            if ($roleActionList instanceof RoleActionList) {
                if ($reload) {
                    $this->migration->down($name);
                    $handler->operationInfo(new Operation('ACL settings have been flushed for ' . $name . '.'));
                }

                $handler->operationInfo(new Operation('Importing ACL settings for ' . $name . '.'));
                $this->migration->up($name, $roleActionList);
                $handler->operationSuccess(new Operation('The ACL settings have been successfully imported.'));
            } else {
                $handler->operationFailed(new Operation('Skipping importing ACL settings for ' . $name . ' due to invalid returned object from the file.'));
            }
        } catch (\Exception $e) {
            Log::error($e);
            $handler->operationFailed(new Operation($e->getMessage()));
        }
    }

}
