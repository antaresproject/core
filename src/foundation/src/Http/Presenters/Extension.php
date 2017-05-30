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

namespace Antares\Foundation\Http\Presenters;

use Antares\Extension\Config\SettingsFormBuilder;
use Antares\Extension\Contracts\Config\SettingsContract;
use Antares\Extension\Contracts\Config\SettingsFormContract;
use Antares\Extension\Model\ExtensionModel;
use Antares\Foundation\Http\Datatables\Extensions;
use Antares\Foundation\Http\Breadcrumb\Breadcrumb;

class Extension extends Presenter
{

    /**
     * Implementation of extension contract.
     *
     * @var \Antares\Contracts\Extension\Factory
     */
    protected $extension;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * extensions datatable
     *
     * @var Extensions
     */
    protected $extensions = null;

    /**
     * @var SettingsFormBuilder
     */
    protected $formBuilder;

    /**
     * Extension constructor.
     * @param SettingsFormBuilder $formBuilder
     * @param Breadcrumb $breadcrumb
     * @param Extensions $extensions
     */
    public function __construct(SettingsFormBuilder $formBuilder, Breadcrumb $breadcrumb, Extensions $extensions) {
        $this->formBuilder  = $formBuilder;
        $this->breadcrumb   = $breadcrumb;
        $this->extensions   = $extensions;
    }

    /**
     * create table instance
     *
     * @return \Illuminate\View\View
     */
    public function table()
    {
        publish(null, ['/js/extensions.js']);
        $this->breadcrumb->onComponentsList();

        return $this->extensions->render('antares/foundation::extensions.index');
    }

    /**
     * @param ExtensionModel $component
     * @param SettingsFormContract $settingsForm
     * @param SettingsContract $settings
     * @return \Antares\Contracts\Html\Builder
     */
    public function form(ExtensionModel $component, SettingsFormContract $settingsForm, SettingsContract $settings) {
        $this->breadcrumb->onComponentConfigure($component);

        return $this->formBuilder->build($component, $settingsForm, $settings);
    }

}
