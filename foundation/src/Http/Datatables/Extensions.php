<?php

declare(strict_types=1);

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

namespace Antares\Foundation\Http\Datatables;

use Antares\Contracts\Authorization\Authorization;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Manager as ExtensionFactory;
use Antares\Datatables\Services\DataTable;
use Illuminate\Contracts\View\Factory;
use Antares\Datatables\Datatables;
use Antares\Support\Collection;
use HTML;
use URL;
use Closure;

class Extensions extends DataTable {

    /**
     * extension factory
     *
     * @var Factory
     */
    protected $extension;

    /**
     * Extensions constructor.
     * @param Datatables $datatables
     * @param Factory $viewFactory
     * @param ExtensionFactory $extensions
     */
    public function __construct(Datatables $datatables, Factory $viewFactory, ExtensionFactory $extensions) {
        parent::__construct($datatables, $viewFactory);

        $this->extension = $extensions;
    }

    /**
     * @return Collection
     */
    public function query() {
        return $this->extension->getAvailableExtensions();
    }

    /**
     * {@inheritdoc}
     */
    public function ajax() {
        $acl          = app('antares.acl')->make('antares');
        $required     = (array) config('installer.required', []);

        return $this->prepare()
            ->addColumn('id', '')
            ->addColumn('full-name', $this->getNameColumn())
            ->addColumn('status', $this->getStatusColumn())
            ->addColumn('description', $this->getDescriptionColumn())
            ->addColumn('authors', $this->getAuthorsColumn())
            ->addColumn('version', $this->getVersionColumn())
            ->addColumn('action', $this->getActionsColumn($acl, $required))
            ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html() {
        publish(null, ['/foundation/public/js/extensions.js']);

        return $this->setName('Extensions')
            ->addColumn(['data' => 'full-name', 'name' => 'full-name', 'title' => trans('antares/foundation::label.extensions.header.name')])
            ->addColumn(['data' => 'description', 'name' => 'description', 'title' => trans('antares/foundation::label.extensions.header.description')])
            ->addColumn(['data' => 'authors', 'name' => 'authors', 'title' => trans('antares/foundation::label.extensions.header.authors')])
            ->addColumn(['data' => 'version', 'name' => 'version', 'title' => trans('antares/foundation::label.extensions.header.version')])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('antares/foundation::label.extensions.header.status')])
            ->addAction(['name' => 'edit', 'title' => '', 'class' => 'dt-row-actions'])
            ->setDeferedData();
    }

    /**
     * Returns actions column.
     *
     * @param $canActivate
     * @param $canUninstall
     * @param array $required
     * @return \Closure
     */
    protected function getActionsColumn(Authorization $acl, array $required = []) : Closure {
        return function(ExtensionContract $extension) use($acl, $required) {
            $buttons    = [];
            $name       = $extension->getPackage()->getName();
            $isRequired = in_array($name, $required, true);

            $ConfigurationStatuses = [ExtensionContract::STATUS_INSTALLED, ExtensionContract::STATUS_ACTIVATED];

            if ( $acl->can('component-configure') && $this->extension->hasSettingsForm($name) && in_array($extension->getStatus(), $ConfigurationStatuses, true) ) {
                $configureUrl = URL::route(area() . '.extensions.viewer.configuration.get', [
                    'vendor'    => $extension->getVendorName(),
                    'name'      => $extension->getPackageName(),
                    'csrf'      => true,
                ]);

                $label  = trans('antares/foundation::label.extensions.actions.configure');

                $params = [
                    'data-icon' => 'wrench',
                ];

                $buttons[] = (string) HTML::link($configureUrl, $label, $params);
            }

            if ($extension->getStatus() === ExtensionContract::STATUS_AVAILABLE && $acl->can('component-install')) {
                $buttons[] = $this->getButtonLink($extension, 'install', 'plus');
            }
            if ($extension->getStatus() === ExtensionContract::STATUS_INSTALLED && $acl->can('component-activate')) {
                $buttons[] = $this->getButtonLink($extension, 'activate', 'plus');
            }
            if ($extension->getStatus() === ExtensionContract::STATUS_INSTALLED && $acl->can('component-uninstall') && ! $isRequired) {
                $buttons[] = $this->getButtonLink($extension, 'uninstall', 'minus');
            }
            elseif($extension->getStatus() === ExtensionContract::STATUS_ACTIVATED && $acl->can('component-deactivate') && ! $isRequired) {
                $buttons[] = $this->getButtonLink($extension, 'deactivate', 'minus');
            }

            if ($isRequired) {
                return HTML::create('div', '', ['class' => 'disabled'])->get();
            }

            if ( ! count($buttons)) {
                return '';
            }

            $section    = HTML::create('div', HTML::raw(implode('', $buttons)), ['class' => 'mass-actions-menu', 'style' => 'display:none;'])->get();
            $indicators = HTML::create('i', '', ['class' => 'ma-trigger'])->get();

            return '<i class="zmdi zmdi-more"></i>' . HTML::raw(implode('', [$section, $indicators]))->get();
        };
    }

    /**
     * Returns buttons for each row.
     *
     * @param ExtensionContract $extension
     * @param string $action
     * @param string $icon
     * @return string
     */
    protected function getButtonLink(ExtensionContract $extension, string $action, string $icon) : string {
        $actionUrl = URL::route(area() . '.extensions.' . $action, [
            'vendor'    => $extension->getVendorName(),
            'name'      => $extension->getPackageName(),
            'csrf'      => true,
        ]);

        $url    = URL::route(area() . '.extensions.progress.index');
        $label  = trans('antares/foundation::label.extensions.actions.' . $action);

        $params = [
            'class'             => 'bindable component-prompt-modal',
            'data-title'        => trans('antares/foundation::messages.package.' . $action, ['name' => $extension->getPackage()->getPrettyName()]),
            'data-icon'         => $icon,
            'data-action-url'   => $actionUrl,
            'data-description'  => trans('Do you want to run the action?')
        ];

        return (string) HTML::link($url, $label, $params);
    }

    /**
     * Returns status column.
     *
     * @return Closure
     */
    protected function getStatusColumn() : Closure {
        return function(ExtensionContract $extension) {
            switch($extension->getStatus()) {
                case ExtensionContract::STATUS_ACTIVATED:
                    return '<span class="label-basic label-basic--success">Active</span>';
                case ExtensionContract::STATUS_INSTALLED:
                    return '<span class="label-basic label-basic--danger">Inactive</span>';
                case ExtensionContract::STATUS_AVAILABLE:
                default:
                    return '<span class="label-basic label-basic--info">Available</span>';
            }
        };
    }

    /**
     * Returns description column.
     *
     * @return Closure
     */
    protected function getDescriptionColumn() : Closure {
        return function(ExtensionContract $extension) {
            $description = $extension->getPackage()->getDescription();

            return '<span class="dots dots-long" data-toggle="tooltip" data-placement="top" title="' . $description . '">' . $description . '</span>';
        };
    }

    /**
     * Returns name column.
     *
     * @return Closure
     */
    protected function getNameColumn() : Closure {
        return function(ExtensionContract $package) {
            return ucfirst($package->getPackageName());
        };
    }

    /**
     * Returns version column.
     *
     * @return Closure
     */
    protected function getVersionColumn() : Closure {
        return function(ExtensionContract $package) {
            return $package->getPackage()->getPrettyVersion();
        };
    }

    /**
     * Returns authors column.
     *
     * @return Closure
     */
    protected function getAuthorsColumn() : Closure {
        return function(ExtensionContract $extension) {
            $authors = array_map(function($author) {
                return sprintf('%s &lt;%s&gt;', $author['name'], $author['email']);
            }, $extension->getPackage()->getAuthors());

            return implode(', ', $authors);
        };
    }

}
