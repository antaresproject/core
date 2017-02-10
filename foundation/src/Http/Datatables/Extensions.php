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


namespace Antares\Foundation\Http\Datatables;

use Antares\Contracts\Extension\Factory as ExtensionFactory;
use Antares\Datatables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Antares\Datatables\Datatables;
use Antares\Support\Collection;

class Extensions extends DataTable
{

    /**
     * we are ignoring every package form following directory
     * @var String
     */
    private static $ignorePath = 'modules';

    /**
     * hwo many rows per page
     *
     * @var mixed
     */
    public $perPage = 25;

    /**
     * extension factory
     *
     * @var Factory2
     */
    protected $extension = null;

    /**
     * constructing
     * 
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
     * @return Builder
     */
    public function query()
    {

        $extensions = $this->extension->detect();
        $return     = new Collection();
        foreach ($extensions as $name => $extension) {
            if (starts_with($extension['path'], 'base::src/' . self::$ignorePath)) {
                continue;
            }
            $return->push([
                'full_name'   => $extension['full_name'],
                'description' => $extension['description'],
                'author'      => $extension['author'],
                'version'     => $extension['version'],
                'url'         => $extension['url'],
                'name'        => $name,
                'activated'   => $this->extension->activated($name),
                'started'     => $this->extension->started($name),
            ]);
        }
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl          = app('antares.acl')->make('antares');
        $canActivate  = $acl->can('component-activate');
        $canUninstall = $acl->can('component-uninstall');
        $required     = config('installer.required');
        return $this->prepare()
                        ->addColumn('id', '')
                        ->addColumn('url', $this->getDocumenationColumn())
                        ->addColumn('full_name', $this->getNameColumn())
                        ->addColumn('status', $this->getStatusColumn())
                        ->addColumn('description', $this->getDescriptionRow())
                        ->addColumn('action', $this->getActionsColumn($canActivate, $canUninstall, $required))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Extensions')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'full_name', 'name' => 'full_name', 'title' => trans('antares/foundation::label.extensions.header.name')])
                        ->addColumn(['data' => 'description', 'name' => 'description', 'title' => trans('antares/foundation::label.extensions.header.description')])
                        ->addColumn(['data' => 'author', 'name' => 'author', 'title' => trans('antares/foundation::label.extensions.header.author')])
                        ->addColumn(['data' => 'version', 'name' => 'version', 'title' => trans('antares/foundation::label.extensions.header.version')])
                        ->addColumn(['data' => 'url', 'name' => 'url', 'title' => trans('antares/foundation::label.extensions.header.url')])
                        ->addColumn(['data' => 'status', 'name' => 'status', 'title' => trans('antares/foundation::label.extensions.header.status')])
                        ->addAction(['name' => 'edit', 'title' => '', 'class' => 'dt-row-actions'])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * 
     * @return callable
     */
    protected function getActionsColumn($canActivate, $canUninstall, array $required = array())
    {

        return function ($row) use($canActivate, $canUninstall, $required) {

            $html = app('html');
            $btns = [];
            if ((!$row['started'] or ! $row['activated']) and $canActivate) {
                $btns[] = $html->link(handles("antares::extensions/{$row['name']}/activate", ['csrf' => true]), trans('antares/foundation::label.extensions.actions.activate'), ['class' => "triggerable confirm", 'data-title' => trans("Do you really want to activate package?"), 'data-icon' => 'plus', 'data-description' => trans('antares/foundation::messages.package.activation', ['name' => $row['full_name']])]);
            } else {
                if ($canUninstall and ! in_array($row['name'], $required)) {
                    $btns[] = $html->link(handles("antares::extensions/{$row['name']}/uninstall", ['csrf' => true]), trans('antares/foundation::label.extensions.actions.uninstall'), ['class' => "triggerable confirm", 'data-title' => trans("Do you really want to uninstall package?"), 'data-icon' => 'minus', 'data-description' => trans('antares/foundation::messages.package.uninstall', ['name' => $row['full_name']])]);
                }
            }
            if (in_array($row['name'], $required)) {
                return $html->create('div', '', ['class' => 'disabled'])->get();
            }

            if (empty($btns)) {
                return '';
            }
            $section    = $html->create('div', $html->raw(implode('', $btns)), ['class' => 'mass-actions-menu', 'style' => 'display:none;'])->get();
            $indicators = $html->create('i', $this->createCircles(), ['class' => 'ma-trigger'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw(implode('', [$section, $indicators]))->get();
        };
    }

    /**
     * create circle dots action value
     * @return String
     */
    protected function createCircles()
    {
        $html = app('html');
        $html->create('i', '', ['class' => 'zmdi zmdi-more']);
    }

    /**
     * get status column
     * @return String
     */
    protected function getStatusColumn()
    {
        return function ($row) {
            if (!$row['started'] or ! $row['activated']) {
                return '<span class="label-basic label-basic--default">Inactive</span>';
            } else {
                return '<span class="label-basic label-basic--success">ACTIVE</span>';
            }
        };
    }

    /**
     * preparing description column
     * 
     * @return String
     */
    protected function getDescriptionRow()
    {

        return function ($row) {
            return '<span class="dots dots-long" data-toggle="tooltip" data-placement="top" title="' . $row['description'] . '">' . $row['description'] . '</span>';
        };
    }

    /**
     * get documenation column
     * @return String
     */
    protected function getDocumenationColumn()
    {
        return function ($row) {
            if ($row['url'] != '---') {
                return app('html')->link($row['url'], $row['url'], ['target' => '_blank'])->get();
            }
            return $row['url'];
        };
    }

    /**
     * get name column
     * @return String
     */
    protected function getNameColumn()
    {
        return function ($row) {
            if ($row['started']) {
                return app('html')->link(handles("antares::extensions/{$row['name']}/configure"), $row['full_name'])->get();
            }
            return $row['full_name'];
        };
    }

}
