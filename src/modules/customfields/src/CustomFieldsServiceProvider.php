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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Customfields;

use Antares\Customfields\Events\FormReadyHandler;
use Antares\Customfields\Events\FormValidate;
use Antares\Customfields\Http\Handlers\LeftPane;
use Antares\Customfields\Model\Field;
use Antares\Customfields\Model\FieldCategory;
use Antares\Customfields\Model\FieldGroup;
use Antares\Customfields\Model\FieldType;
use Antares\Customfields\Model\FieldTypeOption;
use Antares\Customfields\Model\FieldValidator;
use Antares\Customfields\Model\FieldValidatorConfig;
use Antares\Customfields\Model\FieldView;
use Antares\Customfields\Http\Handlers\FieldsBreadcrumbMenu;
use Antares\Foundation\Support\Providers\ModuleServiceProvider;
use Antares\Customfields\Events\AfterSearchCustomfields;
use Antares\Customfields\Console\CustomfieldSync;
use Antares\Support\Facades\Foundation;
use Antares\Acl\RoleActionList;
use Antares\Acl\Action;
use Antares\Model\Role;

class CustomFieldsServiceProvider extends ModuleServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * The application or extension namespace.
     *
     * @var string|null
     */
    protected $namespace = 'Antares\Customfields\Http\Controllers\Admin';

    /**
     * The application or extension group namespace.
     *
     * @var string|null
     */
    protected $routeGroup = 'antares/customfields';

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'antares.form: ready'       => FormReadyHandler::class,
        'antares.form: validate'    => FormValidate::class,
        'customfields.after.search' => AfterSearchCustomfields::class
    ];

    /**
     * registering service provider
     */
    public function register()
    {
        $this->app->bind('antares.customfields.model', function() {
            return new Field();
        });
        $this->app->bind('antares.customfields.model.view', function() {
            return new FieldView();
        });
        $this->app->bind('antares.customfields.model.category', function() {
            return new FieldCategory();
        });
        $this->app->bind('antares.customfields.model.group', function() {
            return new FieldGroup();
        });
        $this->app->bind('antares.customfields.model.type', function() {
            return new FieldType();
        });
        $this->app->bind('antares.customfields.model.type.option', function() {
            return new FieldTypeOption();
        });
        $this->app->bind('antares.customfields.model.validator', function() {
            return new FieldValidator();
        });
        $this->app->bind('antares.customfields.model.validator.config', function() {
            return new FieldValidatorConfig();
        });
        $this->commands([
            CustomfieldSync::class
        ]);
    }

    /**
     * Boot extension components.
     *
     * @return void
     */
    protected function bootExtensionComponents()
    {
        $path = __DIR__ . '/../resources';

        $view = $this->app->make('view');
        $view->composer(['antares/customfields::admin.list', 'antares/customfields::admin.edit'], LeftPane::class);

        $this->addConfigComponent('antares/customfields', 'antares/customfields', "{$path}/config");
        $this->addLanguageComponent('antares/customfields', 'antares/customfields', "{$path}/lang");
        $this->addViewComponent('antares/customfields', 'antares/customfields', "{$path}/views");
        $this->bootMemory();
        $this->bootFormEvents();
        $this->bootMenu();
        //$this->app['antares.customfields.installed'] = true;
    }

    /**
     * Boot extension routing.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        $path = __DIR__;
        $this->loadBackendRoutesFrom("{$path}/routes.php");
    }

    /**
     * booting events
     */
    protected function bootMemory()
    {
        $this->app->make('antares.acl')->make('antares/customfields')->attach($this->app->make('antares.platform.memory'));
    }

    /**
     * booting form events
     */
    protected function bootFormEvents()
    {
        $categoriesCollection = Foundation::make('antares.customfields.model.category')
                        ->select(['name'])->with(['group' => function($query) {
                        $query->select(['name']);
                    }])->get();

        $formHandler      = 'Antares\Customfields\Events\FormHandler';
        $validatorHandler = 'Antares\Customfields\Events\ValidatorHandler';
        $processorHandler = 'Antares\Customfields\Events\ProcessorHandler';


        $categoriesCollection->each(function($category) use($formHandler, $validatorHandler, $processorHandler) {
            $category->group->each(function($group) use($category, $formHandler, $validatorHandler, $processorHandler) {
                $accessor = implode('.', [$category->name, $group->name]);
                $events   = $this->app->make('events');
                $events->listen('antares.form: ' . $accessor, "{$formHandler}@onViewForm");
                $events->listen($accessor . '.customfields.validate', "{$validatorHandler}@onSubmitForm");
                $events->listen('antares.form: ' . $accessor . '.save', "{$processorHandler}@onSave");
            });
        });
    }

    /**
     * booting top left menu
     */
    public function bootMenu()
    {
        $this->attachMenu(FieldsBreadcrumbMenu::class);
    }

    /**
     * @return RoleActionList
     */
    public static function acl()
    {
        $actions = [
            new Action('admin.customfields.index', 'List Customfields'),
            new Action('admin.customfields.create', 'Add Customfield'),
            new Action('admin.customfields.edit', 'Update Customfield'),
            new Action('admin.customfields.destroy', 'Delete Customfield'),
        ];

        $permissions = new RoleActionList;
        $permissions->add(Role::admin()->name, $actions);
        return $permissions;
    }

}
