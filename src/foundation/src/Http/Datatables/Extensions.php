<?php

declare(strict_types = 1);

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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Foundation\Http\Datatables;

use Antares\Contracts\Authorization\Authorization;
use Antares\Datatables\Adapter\GroupsFilterAdapter;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Manager as ExtensionFactory;
use Antares\Datatables\Services\DataTable;
use Antares\Extension\Model\Types;
use Illuminate\Contracts\View\Factory;
use Antares\Datatables\Datatables;
use Antares\Support\Collection;
use Closure;
use HTML;
use URL;

class Extensions extends DataTable
{

    /**
     * extension factory
     *
     * @var Factory
     */
    protected $extension;

    /**
     * Index of type column.
     *
     * @var int
     */
    protected $typeColumnIndex = 5;

    /**
     * Extensions constructor.
     * @param Datatables $datatables
     * @param Factory $viewFactory
     * @param ExtensionFactory $extensions
     */
    public function __construct(Datatables $datatables, Factory $viewFactory, ExtensionFactory $extensions)
    {
        parent::__construct($datatables, $viewFactory);

        $this->extension = $extensions;
    }

    /**
     * @return Collection
     */
    public function query()
    {
        $collection = $this->extension->getAvailableExtensions();

        return $this->filterByType($collection);
    }

    /**
     * @return GroupsFilterAdapter
     */
    protected function getGroupsFilterAdapter(): GroupsFilterAdapter
    {
        $groupsFilterAdapter = app(GroupsFilterAdapter::class);
        $groupsFilterAdapter->setClassname(get_class($this))->setIndex($this->typeColumnIndex);

        return $groupsFilterAdapter;
    }

    /**
     * @param \Antares\Extension\Collections\Extensions $query
     * @return \Antares\Extension\Collections\Extensions
     */
    protected function filterByType(\Antares\Extension\Collections\Extensions $query)
    {
        $value = $this->getGroupsFilterAdapter()->getSelected($this->typeColumnIndex);
        if (is_null($value)) {
            return $query->filter(function(ExtensionContract $extension) use($value) {
                        return $extension->getFriendlyType() === 'Additional';
                    });
        }
        if (request()->ajax() || $value === '') {
            return $query;
        }
        return $query->filter(function(ExtensionContract $extension) use($value) {
                    return $extension->getFriendlyType() === $value;
                });
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl = app('antares.acl')->make('antares');

        return $this->prepare()
                        ->addColumn('id', '')
                        ->addColumn('friendlyName', $this->getNameColumn())
                        ->addColumn('status', $this->getStatusColumn())
                        ->addColumn('description', $this->getDescriptionColumn())
                        ->addColumn('authors', $this->getAuthorsColumn())
                        ->addColumn('version', $this->getVersionColumn())
                        ->addColumn('type', $this->getTypeColumn())
                        ->addColumn('action', $this->getActionsColumn($acl))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        publish(null, ['/foundation/public/js/extensions.js']);

        $options = [
            'data-prefix' => trans('antares/foundation::messages.select_extension_type'),
        ];

        return $this->setName('Extensions')
                        ->addColumn(['data' => 'friendlyName', 'name' => 'friendlyName', 'title' => trans('antares/foundation::label.extensions.header.name')])
                        ->addColumn(['data' => 'description', 'name' => 'description', 'title' => trans('antares/foundation::label.extensions.header.description')])
                        ->addColumn(['data' => 'authors', 'name' => 'authors', 'title' => trans('antares/foundation::label.extensions.header.authors'), 'orderable' => false])
                        ->addColumn(['data' => 'version', 'name' => 'version', 'title' => trans('antares/foundation::label.extensions.header.version')])
                        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('antares/foundation::label.extensions.header.status')])
                        ->addColumn(['data' => 'type', 'name' => 'type', 'title' => trans('antares/foundation::label.extensions.header.type')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'dt-row-actions'])
                        ->addGroupSelect($this->getTypes(), $this->typeColumnIndex, Types::TYPE_ADDITIONAL, $options)
                        ->parameters([
                            'aoColumnDefs' => [
                                ['width' => '15%', 'targets' => 0],
                                ['width' => '40%', 'targets' => 1],
                                ['width' => '22%', 'targets' => 2],
                                ['width' => '7%', 'targets' => 3],
                                ['width' => '10%', 'targets' => 4],
                                ['width' => '10%', 'targets' => 5],
                                ['width' => '1%', 'targets' => 6],
                    ]])->ajax(handles('antares/foundation::/modules'));
    }

    /**
     * @return array
     */
    protected function getTypes(): array
    {
        $types = [
            '' => trans('antares/foundation::messages.extension_type_all'),
        ];

        foreach (Types::all() as $type) {
            $types[$type] = $type;
        }

        return $types;
    }

    /**
     * @param Authorization $acl
     * @return Closure
     */
    protected function getActionsColumn(Authorization $acl): Closure
    {
        return function(ExtensionContract $extension) use($acl) {
            $buttons = [];
            $name    = $extension->getPackage()->getName();

            if ($extension->isRequired()) {
                return HTML::create('div', '', ['class' => 'disabled'])->get();
            }

            if ($acl->can('component-configure') && $this->extension->hasSettingsForm($name) && $extension->getStatus() === ExtensionContract::STATUS_ACTIVATED) {
                $configureUrl = URL::route(area() . '.modules.viewer.configuration.get', [
                            'vendor' => $extension->getVendorName(),
                            'name'   => $extension->getPackageName(),
                            'csrf'   => true,
                ]);

                $label = trans('antares/foundation::label.extensions.actions.configure');

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

            if (!$extension->isRequired()) {
                if ($extension->getStatus() === ExtensionContract::STATUS_INSTALLED && $acl->can('component-uninstall')) {
                    $buttons[] = $this->getButtonLink($extension, 'uninstall', 'minus');
                } elseif ($extension->getStatus() === ExtensionContract::STATUS_ACTIVATED && $acl->can('component-deactivate')) {
                    $buttons[] = $this->getButtonLink($extension, 'deactivate', 'minus');
                }
            }

            if (!count($buttons)) {
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
    protected function getButtonLink(ExtensionContract $extension, string $action, string $icon): string
    {
        $actionUrl = URL::route(area() . '.modules.' . $action, [
                    'vendor' => $extension->getVendorName(),
                    'name'   => $extension->getPackageName(),
                    'csrf'   => true,
        ]);

        $name  = $extension->getFriendlyName();
        $url   = URL::route(area() . '.modules.progress.index');
        $label = trans('antares/foundation::label.extensions.actions.' . $action);

        $params = [
            'class'            => 'bindable component-prompt-modal',
            'data-title'       => trans('antares/foundation::messages.extensions.modal_title.' . $action),
            'data-icon'        => $icon,
            'data-action-url'  => $actionUrl,
            'data-description' => trans('antares/foundation::messages.extensions.modal_content.' . $action, compact('name')),
        ];

        return (string) HTML::link($url, $label, $params);
    }

    /**
     * Returns status column.
     *
     * @return Closure
     */
    protected function getStatusColumn(): Closure
    {
        return function(ExtensionContract $extension) {
            switch ($extension->getStatus()) {
                case ExtensionContract::STATUS_ACTIVATED:
                    return '<span class="label-basic label-basic--success">' . trans('antares/foundation::label.extensions.statuses.active') . '</span>';

                case ExtensionContract::STATUS_INSTALLED:
                    return '<span class="label-basic label-basic--danger">' . trans('antares/foundation::label.extensions.statuses.inactive') . '</span>';

                case ExtensionContract::STATUS_AVAILABLE:
                default:
                    return '<span class="label-basic label-basic--info">' . trans('antares/foundation::label.extensions.statuses.available') . '</span>';
            }
        };
    }

    /**
     * Returns description column.
     *
     * @return Closure
     */
    protected function getDescriptionColumn(): Closure
    {
        return function(ExtensionContract $extension) {
            return $extension->getPackage()->getDescription();
        };
    }

    /**
     * Returns name column.
     *
     * @return Closure
     */
    protected function getNameColumn(): Closure
    {
        return function(ExtensionContract $package) {
            $name = $package->getFriendlyName();
            $url  = $package->getPackage()->getHomepage();

            if ($url) {
                return (string) HTML::link($url, $name, [
                            'target' => '_blank',
                ]);
            }

            return $name;
        };
    }

    /**
     * Returns version column.
     *
     * @return Closure
     */
    protected function getVersionColumn(): Closure
    {
        return function(ExtensionContract $package) {
            return $package->getPackage()->getPrettyVersion();
        };
    }

    /**
     * Returns version column.
     *
     * @return Closure
     */
    protected function getTypeColumn(): Closure
    {
        return function(ExtensionContract $package) {
            return $package->getFriendlyType();
        };
    }

    /**
     * Returns authors column.
     *
     * @return Closure
     */
    protected function getAuthorsColumn(): Closure
    {
        return function(ExtensionContract $extension) {
            $authors = array_map(function(array $author) {
                if (array_key_exists('email', $author)) {
                    return (string) HTML::mailto($author['email'], $author['name']);
                }

                return $author['name'];
            }, $extension->getPackage()->getAuthors());

            $prefix = '';

            if ($extension->getVendorName() === 'antaresproject') {
                $prefix = trans('antares/foundation::title.antares_team');
            }

            return $prefix . ' - ' . implode(', ', $authors);
        };
    }

}
