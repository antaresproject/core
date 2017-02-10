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


namespace Antares\Foundation\Http\Presenters;

use Antares\Contracts\Extension\Factory as ExtensionContract;
use Antares\Contracts\Html\Form\Builder;
use Antares\Contracts\Html\Form\Factory;
use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;
use Antares\Support\Collection;
use Eloquent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use TwigBridge\Extension\Laravel\Form;
use function app;
use function data_get;
use function handles;
use function head;
use function starts_with;
use function trans;

class Module extends Presenter
{

    use DropzoneTrait;

    /**
     * we are ignoring every package form following directory
     * @var String
     */
    private static $ignorePath = 'components';

    /**
     * Implementation of extension contract.
     *
     * @var ExtensionContract
     */
    protected $extension;

    /**
     * module category
     *
     * @var String
     */
    protected $category;

    /**
     * breadcrumb instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Construct a new Extension presenter.
     *
     * @param  ExtensionContract  $extension
     * @param  Factory  $form
     */
    public function __construct(ExtensionContract $extension, FormFactory $form, Breadcrumb $breadcrumb)
    {
        $this->form       = $form;
        $this->extension  = $extension;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * sets module category
     * @param String $category
     * @return Module
     */
    public function setCategory($category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Form View Generator for Antares\Extension.
     *
     * @param  Fluent  $model
     * @param  string  $name
     *
     * @return Builder
     */
    public function configure($model, $name)
    {
        $this->breadcrumb->onModuleConfigure($name);
        return $this->form->of("antares.extension: {$name}", function (FormGrid $form) use ($model, $name) {
                    $form->setup($this, "antares::modules/{$this->category}/{$name}/configure", $model);

                    $handles      = data_get($model, 'handles', $this->extension->option($name, 'handles'));
                    $configurable = data_get($model, 'configurable', true);

                    $form->fieldset(function (Fieldset $fieldset) use ($handles, $name, $configurable) {
                        if (!is_null($handles) && $configurable !== false) {
                            $fieldset->control('input:text', 'handles')
                                    ->label(trans('antares/foundation::label.extensions.handles'))
                                    ->value($handles);
                        }

                        $fieldset->control('input:text', 'migrate')
                                ->label(trans('antares/foundation::label.extensions.update'))
                                ->field(function () use ($name) {
                                    return app('html')->link(
                                                    handles("antares::modules/{$this->category}/{$name}/update", ['csrf' => true]), trans('antares/foundation::label.extensions.actions.update'), ['class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect']
                                    );
                                });
                    });
                });
    }

    /**
     * get all active modules collection
     * 
     * @return Collection
     */
    public function modules()
    {
        $this->breadcrumb->onModulesList();
        $extensions = app('antares.memory')->make('component.default')->get('extensions');
        $active     = isset($extensions['active']) ? array_keys($extensions['active']) : [];
        $return     = new Collection();

        foreach ($extensions['available'] as $name => $extension) {
            if (starts_with($extension['path'], 'base::src/' . self::$ignorePath)) {
                continue;
            }

            $descriptor = [
                'full_name'   => $extension['full_name'],
                'description' => $extension['description'],
                'author'      => $extension['author'],
                'version'     => $extension['version'],
                'url'         => $extension['url'],
                'name'        => $name,
                'activated'   => in_array($name, $active),
                'started'     => $this->extension->started($name),
                'category'    => $this->resolveModuleCategoryName($name)
            ];


            if (is_null($this->category) OR starts_with($name, $this->category)) {
                $return->push($descriptor);
            }
        }
        return $return;
    }

    /**
     * creates dynamic module uploader form
     * @param Eloquent $model
     * @param String $category
     * @param Validator $validator
     * @return Form | mixed
     */
    public function create($model, $category = null, $validator = null)
    {
        $this->breadcrumb->onCreate();
        $name                            = 'file';
        $rules                           = $this->getValidationRules($name, $validator);
        $attributes                      = ['container' => 'myAwesomeDropzone', 'paramName' => $name, 'url' => handles("antares::modules/upload"),] + $rules;
        $attributes['previewsContainer'] = '#DropZonePreviewContainer';


        return $this->form->of("antares.extension: {$category}", function (FormGrid $form) use ($category, $model, $attributes) {
                    $form->name('Module Install');
                    $url = $category == null ? "antares::modules/create" : "antares::modules/{$category}/create";
                    $form->resource($this, $url, $model, ['class' => 'dropzone', 'enctype' => 'multipart/form-data', 'id' => $attributes['container']]);
                    $form->fieldset(function (Fieldset $fieldset) use($attributes) {
                        $fieldset->control('dropzone', 'module-source')
                                ->attributes($attributes);
                    });
                    $form->layout('antares/foundation::modules.form');
                });
    }

    /**
     * resolving module category name by module namespace
     * 
     * @param String $module
     * 
     * @return String
     */
    protected function resolveModuleCategoryName($name)
    {
        return head(explode('/', $name));
    }

}
