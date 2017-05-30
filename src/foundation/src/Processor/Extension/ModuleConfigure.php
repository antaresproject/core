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


namespace Antares\Foundation\Processor\Extension;

use Illuminate\Support\Fluent;
use Illuminate\Support\Facades\Event;
use Antares\Support\Facades\Extension;
use Antares\Support\Facades\Foundation;
use Antares\Foundation\Processor\Processor;
use Antares\Foundation\Validation\Module as ModuleValidator;
use Antares\Contracts\Extension\Command\Configure as Command;
use Antares\Foundation\Http\Presenters\Module as Presenter;
use Antares\Contracts\Extension\Listener\Configure as Listener;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Antares\Foundation\Validation\CustomModule;
use Illuminate\Support\Facades\Log;

class ModuleConfigure extends Processor implements Command
{

    /**
     * module category
     * 
     * @var String
     */
    protected $category = null;

    /**
     * Create a new processor instance.
     *
     * @param  \Antares\Foundation\Http\Presenters\Extension  $presenter
     * @param  \Antares\Foundation\Validation\Extension  $validator
     */
    public function __construct(Presenter $presenter, ModuleValidator $validator)
    {
        $this->presenter = $presenter;
        $this->validator = $validator;
    }

    /**
     * sets module category
     * 
     * @param String $category
     * @return \Antares\Foundation\Processor\Extension\ModuleConfigure
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * View edit extension configuration page.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     *
     * @return mixed
     */
    public function configure(Listener $listener, Fluent $extension)
    {
        if (!Extension::started($extension->get('name'))) {
            return $listener->abortWhenRequirementMismatched();
        }
        $this->presenter->setCategory($this->category);

        $memory = Foundation::memory();

        $activeConfig = (array) $memory->get("extensions.active.{$extension->get('name')}.config", []);
        $baseConfig   = (array) $memory->get("extension_{$extension->get('name')}", []);


        $eloquent = new Fluent(array_merge($activeConfig, $baseConfig));

        $form = $this->presenter->configure($eloquent, $extension->get('name'));

        Event::fire("antares.form: extension.{$extension->get('name')}", [$eloquent, $form]);

        return $listener->showConfigurationChanger(compact('eloquent', 'form', 'extension'));
    }

    /**
     * Update an extension configuration.
     *
     * @param  \Antares\Contracts\Extension\Listener\Configure  $listener
     * @param  \Illuminate\Support\Fluent  $extension
     * @param  array  $input
     *
     * @return mixed
     */
    public function update(Listener $listener, Fluent $extension, array $input)
    {

        if (!Extension::started($extension->get('name'))) {
            return $listener->suspend(404);
        }

        $validation = $this->validator->with($input, ["antares.validate: extension.{$extension->get('name')}"]);

        if ($validation->fails()) {
            return $listener->updateConfigurationFailedValidation($validation->getMessageBag(), $extension->uid);
        }

        $memory = Foundation::memory();
        $config = (array) $memory->get("extension.active.{$extension->get('name')}.config", []);
        $input  = new Fluent(array_merge($config, $input));

        unset($input['_token']);

        Event::fire("antares.saving: extension.{$extension->get('name')}", [& $input]);

        $memory->put("extensions.active.{$extension->get('name')}.config", $input->getAttributes());
        $memory->put("extension_{$extension->get('name')}", $input->getAttributes());

        Event::fire("antares.saved: extension.{$extension->get('name')}", [$input]);

        return $listener->configurationUpdated($extension);
    }

    /**
     * resolving module category name by module real name
     * 
     * @param Fluent $module
     * 
     * @return String
     */
    public function resolveModuleCategoryName(Fluent $module)
    {
        return head(explode('/', $module->name));
    }

    /**
     * create module form
     * 
     * @param String $category
     * @return type
     */
    public function create(Listener $listener, $category = null)
    {
        $eloquent = Foundation::make('antares.component');
        $form     = $this->presenter->create($eloquent, $category, $this->validator);
        Event::fire("antares.form: extension.create", [$eloquent, $form]);

        return $listener->showModuleCreator(compact('form', 'category'));
    }

    /**
     * upload file temporary resolver
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return type
     */
    protected function resolveTempFileName(UploadedFile $file)
    {
        $name      = $file->getClientOriginalName();
        $extension = File::extension($name);
        $subdir    = sha1(time());
        $directory = storage_path() . '/app/uploads/' . $subdir;
        $filename  = sha1(time() . time()) . ".{$extension}";
        return ['directory' => $directory, 'subdir' => $subdir, 'filename' => $filename];
    }

    /**
     * upload and validate module compressed file
     * 
     * @param array $input
     * @return \Illuminate\Support\Facades\Response
     */
    public function upload(array $input)
    {
        $file                     = $this->resolveTempFileName($input['file']);
        $input['file']->directory = $file['directory'];
        $input['file']->filename  = $file['filename'];
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new CustomModule($translator, $data, $rules, $messages);
        });

        $validation = $this->validator->on('upload')->with($input, [], [
            'source' => trans('antares/foundation::response.modules.validator.invalid-structure')
        ]);


        return ($validation->fails()) ? Response::make($validation->getMessageBag()->first(), 400) :
                Response::json([
                    'html' => $this->manifestDecorator($validation->getManifest()),
                    'path' => $file['subdir'] . DIRECTORY_SEPARATOR . $file['filename']], 200);
    }

    /**
     * generate view of manifest description
     * 
     * @param array $manifest
     * @return String
     */
    protected function manifestDecorator(array $manifest)
    {
        return view('antares/foundation::modules.manifest', ['manifest' => $manifest])->render();
    }

    /**
     * unziping module package
     * 
     * @param String $source
     * @param String $destination
     * @return boolean
     * @throws \Exception
     */
    protected function unzip($source, $destination)
    {
        @mkdir($destination, 0777, true);
        $zip = new \ZipArchive;
        if ($zip->open($source) === true) {
            $zip->extractTo($destination);
            $zip->close();
            return true;
        } else {
            throw new \Exception('Unable to open module package file');
        }
    }

    /**
     * extraction of module package
     * 
     * @param Listener $listener
     * @param String $category
     * @param array $input
     */
    public function extract(Listener $listener, $category = null, array $input)
    {

        try {
            $subdir = !is_null($category) ? $category : 'addons';
            $target = realpath(app_path() . "/../src/modules/{$subdir}");
            if (!is_dir($target)) {
                throw new \Exception('Invalid path of module provided');
            }

            $name = $input['name'];
            $path = storage_path() . '/app/uploads/' . $name;
            if (!file_exists($path)) {
                throw new \Exception('Invalid path of module package');
            }

            if ($this->unzip($path, $target)) {
                return $listener->moduleExtracted($category);
            }
            return $listener->moduleExtractionError($category);
        } catch (\Exception $e) {
            Log::emergency($e);
            return $listener->moduleExtractionError($category);
        }
    }

}
