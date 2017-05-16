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



namespace Antares\Translations\Composers;

use Illuminate\Contracts\Foundation\Application;

class LanguageSelector
{

    /**
     * @var Application 
     */
    protected $app;

    /**
     * constructing
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * up component placeholders
     */
    public function handle()
    {
        $languages = $this->app->make('languages');
        $list      = $languages->all();
        if (!$list->count()) {
            return;
        }
        $current = $languages->current();
        $this->app->make('antares.widget')
                ->make('placeholder.languages')
                ->add('languages')
                ->value(view('antares/translations::admin.partials._language_select', ['languages' => $list, 'current' => $current]));
    }

}
