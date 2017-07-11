<?php

declare(strict_types=1);

namespace Antares\Foundation\Console\Commands;

use Antares\Brands\Model\Brands;
use Antares\Model\Action;
use Antares\Model\Component;
use Antares\Model\Permission;
use Antares\Model\Role;
use Illuminate\Console\Command;

class CoreAclCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'antares:acl:reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh antares core ACL.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $role       = Role::admin();
        $brands     = Brands::all();
        $core       = Component::findByVendorAndName('antaresproject', 'core');
        $actions    = $this->getActions($core);

        foreach($brands as $brand) {
            $this->removePermissions($brand, $role, $core);

            foreach($actions as $action) {
                $this->setActionToBrand($brand, $role, $action);
            }
        }

        $this->info('The core permissions has been successfully reloaded.');
    }

    /**
     * Returns actions for the given component.
     *
     * @param Component $component
     * @return Action[]
     */
    private function getActions(Component $component) {
        $defaultActions = (array) config('antares/installer::permissions.components.' . $component->name, []);
        $actions        = $this->mapActionsToFlat($defaultActions);

        return Action::where('component_id', $component->id)->whereIn('name', $actions)->get();
    }

    /**
     * @param array $actions
     * @return array
     */
    private function mapActionsToFlat(array $actions) : array {
        $toReturn = [];

        foreach($actions as $name => $value) {
            if(is_array($value)) {
                $toReturn = array_merge($toReturn, $this->mapActionsToFlat($value));
            }
            else {
                $toReturn[] = $name;
            }
        }

        return $toReturn;
    }

    /**
     * @param Brands $brand
     * @param Role $role
     * @param Component $component
     */
    private function removePermissions(Brands $brand, Role $role, Component $component) {
        Permission::where('brand_id', $brand->id)
            ->where('component_id', $component->id)
            ->where('role_id', $role->id)
            ->delete();
    }

    /**
     * @param Brands $brand
     * @param Role $role
     * @param Action $action
     */
    private function setActionToBrand(Brands $brand, Role $role, Action $action) {
        $fill = [
            'brand_id'     => $brand->id,
            'component_id' => $action->component_id,
            'role_id'      => $role->id,
            'action_id'    => $action->id,
            'allowed'      => 1,
        ];

        Permission::create($fill);
    }
}
