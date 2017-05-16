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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Translations\Processor;

use Antares\Translations\Http\Datatables\Languages as LanguagesDatatable;
use Antares\Translations\Contracts\LanguagePresenter as Presenter;
use Antares\Translations\Repository\TranslationRepository;
use Illuminate\Http\UploadedFile as LaravelUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Antares\Translations\Repository\LanguageRepository;
use Antares\Translations\Validator\CsvCustomValidator;
use Antares\Translations\Contracts\LanguageListener;
use Antares\Translations\Http\Breadcrumb\Breadcrumb;
use Antares\Translations\Validator\CsvValidator;
use Antares\Translations\Processor\Publisher;
use Antares\Foundation\Processor\Processor;
use Antares\Translations\Models\Languages;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class LanguageProcessor extends Processor
{

    /**
     * Publisher instance
     *
     * @var Publisher
     */
    protected $publisher;

    /**
     * CsvValidator instance
     *
     * @var CsvValidator
     */
    protected $validator;

    /**
     * LanguageRepository instance
     *
     * @var LanguageRepository
     */
    protected $languageRepository;

    /**
     * TranslationRepository instance
     * 
     * @var TranslationRepository 
     */
    protected $translationRepository;

    /**
     * filesystem instance
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param Publisher $publisher
     * @param CsvValidator $validator
     * @param LanguageRepository $languageRepository
     * @param TranslationRepository $translationRepository
     * @param Filesystem $filesystem
     */
    public function __construct(Presenter $presenter, Publisher $publisher, CsvValidator $validator, LanguageRepository $languageRepository, TranslationRepository $translationRepository, Filesystem $filesystem)
    {
        $this->presenter             = $presenter;
        $this->publisher             = $publisher;
        $this->validator             = $validator;
        $this->languageRepository    = $languageRepository;
        $this->translationRepository = $translationRepository;
        $this->filesystem            = $filesystem;
    }

    /**
     * Shows languages list
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        app(Breadcrumb::class)->onLanguagesList();
        return app(LanguagesDatatable::class)->render('antares/translations::admin.language.index');
    }

    /**
     * create new language form
     * 
     * @return \Illuminate\View\View
     */
    public function create(LanguageListener $listener, Request $request)
    {
        if ($request->isMethod('post')) {
            $form = $this->presenter->form();
            if (!$form->isValid()) {
                return $listener->createFailed($form->getMessageBag());
            }
            try {
                $data = $form->getData();
                $this->languageRepository->insert($data);
            } catch (Exception $ex) {
                Log::warning($e);
                return $listener->createFailed($ex->getMessage());
            }
            return $listener->createSucceed();
        }
        return $this->presenter->create();
    }

    /**
     * processing publish action
     * 
     * @param LanguageListener $listener
     * @param mixed $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publish(LanguageListener $listener, $type)
    {
        try {
            $languages = Languages::all();
            foreach ($languages as $language) {
                $this->publisher->publish($language);
            }
            return $listener->publishSucceed($type);
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->publishFailed($type, $e->getMessage());
        }
    }

    /**
     * processing import action
     * 
     * @param LanguageListener $listener
     * @param String $type
     * @param String $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(LanguageListener $listener, $type, $locale = null)
    {

        $input        = Input::all();
        $locale       = is_null($locale) ? app()->getLocale() : $locale;
        $uploadedFile = array_get($input, 'file');
        if (!app('request')->isMethod('post')) {
            return $this->presenter->import();
        } elseif (!is_null($uploadedFile) and ( $uploadedFile instanceof LaravelUploadedFile)) {
            return $this->uploadTemporary($uploadedFile, $input);
        } elseif (!is_null($uploadedFile) and is_string($uploadedFile)) {
            return ($this->translationRepository->importTranslations($uploadedFile, $type)) ? $listener->importSuccess($type, $locale) : $listener->importFailed();
        }
        return $listener->importFailed();
    }

    /**
     * Validates and uploads csv file do temporary directory
     * 
     * @param LaravelUploadedFile $uploadedFile
     * @param array $input
     * @return Response
     */
    protected function uploadTemporary(LaravelUploadedFile $uploadedFile, array $input)
    {
        $file                    = $this->resolveTempFileName($uploadedFile);
        $uploadedFile->directory = $file['directory'];
        $uploadedFile->filename  = $file['filename'];

        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new CsvCustomValidator($translator, $data, $rules, $messages);
        });
        $validation = $this->validator->on('upload')->with($input, [], [
            'source' => trans('antares/foundation::response.modules.validator.invalid-structure')
        ]);
        if ($validation->fails()) {
            return Response::make($validation->getMessageBag()->first(), 400);
        }
        return ($uploadedFile->move($uploadedFile->directory, $uploadedFile->filename)) ?
                Response::json(['path' => $uploadedFile->directory . DIRECTORY_SEPARATOR . $uploadedFile->filename], 200) :
                Response::make(trans('Unable to upload file.'), 400);
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
     * processing export action
     * 
     * @param LanguageListener $listener
     * @param String $locale
     * @param String $type
     * @return \Illuminate\Http\RedirectResponse
     */
    public function export(LanguageListener $listener, $locale, $type)
    {
        try {
            $separator    = app('config')->get('antares/translations::export.separator');
            $translations = Languages::where('code', $locale)->whereHas('translations', function($query) use($type) {
                        $query->where('area', $type)->orderBy('key', 'desc');
                    })->first()->translations;

            $lines = [implode($separator, ['Segment', 'Locale', 'Key', 'Translation'])];
            foreach ($translations as $translation) {
                $data = [
                    str_replace('antares/', '', $translation->group),
                    $locale,
                    '"' . $translation->key . '"',
                    '"' . $translation->value . '"',
                ];
                array_push($lines, implode($separator, $data));
            }
            $filename = 'export_' . date('Y_m_d_H_i_s') . '_' . $locale . '.csv';

            return Response::make(implode(PHP_EOL, $lines), '200', array(
                        'Content-Type'        => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="' . $filename
            ));
        } catch (Exception $e) {
            dd($e);
            Log::warning($e);
            return $listener->exportFailed($e->getMessage());
        }
    }

    /**
     * processing change active language
     * 
     * @param LanguageListener $listener
     * @param String $locale
     * @return RedirectResponse
     */
    public function change(LanguageListener $listener, $locale)
    {
        try {
            Session::put('locale', $locale);
            app()->setLocale($locale);
            return $listener->changeSuccess();
        } catch (Exception $e) {
            Log::warning($e);
            return $listener->changeFailed($e->getMessage());
        }
    }

    /**
     * deleteing language
     * 
     * @param LanguageListener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(LanguageListener $listener, $id)
    {
        DB::beginTransaction();
        try {

            $language = Languages::query()->findOrFail($id);
            if ($language->is_default) {
                throw new Exception(trans('antares/translations::messages.unable_to_delete_default_language'));
            }
            $code = $language->code;
            $language->delete();

            $vendor = base_path('src');
            foreach (['core/foundation', 'core/licensing'] as $dir) {
                $path = implode(DIRECTORY_SEPARATOR, [$vendor, $dir, 'resources', 'lang', $code]);
                $this->deleteLangFiles($path);
            }
            $directories = $this->filesystem->directories($vendor . DIRECTORY_SEPARATOR . 'components');
            foreach ($directories as $directory) {
                $path = implode(DIRECTORY_SEPARATOR, [$directory, 'resources', 'lang', $code]);
                $this->deleteLangFiles($path);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $listener->deleteFailed($e->getMessage());
        }
        DB::commit();
        return $listener->deleteSuccess();
    }

    /**
     * deleteing lang files
     * 
     * @param String $path
     * @return boolean
     */
    protected function deleteLangFiles($path)
    {
        if (is_dir($path)) {
            $this->filesystem->cleanDirectory($path);
            $this->filesystem->deleteDirectory($path);
            return true;
        }
        return false;
    }

    /**
     * Sets default language
     * 
     * @param LanguageListener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setDefault(LanguageListener $listener, $id)
    {
        DB::beginTransaction();
        try {
            $current             = Languages::query()->where('is_default', 1)->firstOrFail();
            $current->is_default = 0;
            $current->save();

            $language             = Languages::query()->findOrFail($id);
            $language->is_default = 1;
            $language->save();
        } catch (Exception $ex) {
            DB::rollback();
            return $listener->defaultFailed($ex->getMessage());
        }
        DB::commit();
        return $listener->defaultSuccess();
    }

}
