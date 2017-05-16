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

namespace Antares\Translations\Http\Datatables;

use Antares\Translations\Repository\TranslationRepository;
use Antares\Datatables\Services\DataTable;
use Antares\Translations\Models\Languages;
use Illuminate\Database\Eloquent\Builder;

class Translations extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * @return Builder
     */
    public function query()
    {
        $locale = $this->getLocale();
        $id     = from_route('id');
        return app(TranslationRepository::class)->getList($id, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $keyUrl         = handles('antares::translations/update-key/' . from_route('id'));
        $translationUrl = handles('antares::translations/update-translation/' . from_route('id'));
        $deleteUrl      = handles('antares::translations/delete-translation/' . from_route('id'));

        $canEditTranslation = app('antares.acl')->make('antares/translations')->can('edit-translation');
        return $this->prepare()
                        ->editColumn('key', function ($item) use($keyUrl) {
                            return '<div class="table-key__mobile-open">
                                        <i class="zmdi zmdi-chevron-down"></i>
                                    </div>
                                    <div class="table-key__inner">
                                        <div class="table-key__text">' . $item->key . '</div>
                                        <textarea data-url="' . $keyUrl . '" data-id="' . $item->id . '" rows=\'1\' class="table-key__input"></textarea>
                                        <div class="table-key__actions">
                                            <div class="table-key__remove"><i class="zmdi zmdi-close"></i></div>
                                            <div class="table-key__add"><i class="zmdi zmdi-check"></i></div>
                                        </div>
                                        <div class="table-key__init-edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </div>
                                    </div>';
                        })
                        ->editColumn('value', function ($item) use($translationUrl) {
                            return '<div class="table-key__inner">
                                        <div class="table-key__text">' . $item->value . '</div>
                                        <textarea data-url="' . $translationUrl . '" data-id="' . $item->id . '"  rows=\'1\' class="table-key__input">' . $item->value . '</textarea>
                                        <div class="table-key__actions">
                                            <div class="table-key__remove"><i class="zmdi zmdi-close"></i></div>
                                            <div class="table-key__add"><i class="zmdi zmdi-check"></i></div>
                                        </div>
                                        <div class="table-key__init-edit">
                                            <i class="zmdi zmdi-edit"></i>
                                        </div>
                                    </div>';
                        })
                        ->addColumn('action', function($row) use ($deleteUrl) {
                            return '<div class="table-key__inner"><span>' . $row->value . '</span>
                                    <div class = "table-key__delete-row" data-url = "' . $deleteUrl . '" data-id = "' . $row->id . '">
                                    <i class = "zmdi zmdi-delete"></i>
                                    </div>
                                    </div>';
                        })
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        $builder = $this
                ->setName('Translations List')
                ->setQuery($this->query())
                ->addColumn(['data' => 'id', 'name' => 'id', 'title' => 'Id', 'searchable' => false, 'orderable' => false, 'class' => 'no-sort'])
                ->addColumn(['data' => 'key', 'name' => 'key', 'title' => 'Key'])
                ->addColumn(['data' => 'value', 'name' => 'value', 'title' => 'Translation'])
                ->addAction(['name' => 'edit', 'title' => '', 'class' => 'inline', 'searchable' => false, 'orderable' => false]);
        $id      = from_route('id');
        $locale  = $this->getLocale();

        $current = Languages::where('code', $locale)->first();

        $url = handles('antares::translations/index/' . $id . '/' . $current->code);
        return $builder->massable(false)
                        ->searchable(false)
                        ->tableAttributes([
                            'class'              => 'translations-table',
                            'data-adjust-height' => true,
                            'data-url'           => $url
                        ])
                        ->containerAttributes(['class' => 'tbl-translations'])
                        ->ajax($url);
    }

    protected function getLocale()
    {
        $code   = from_route('code');
        $locale = is_null($code) ? app()->getLocale() : $code;
        return $locale == null ? 'en' : $locale;
    }

}
