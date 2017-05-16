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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Http\Datatables;

use Antares\Datatables\Services\DataTable;
use Antares\Support\Facades\Foundation;

class Sandboxes extends DataTable
{

    /**
     * items per page
     *
     * @var mixed 
     */
    public $perPage = 25;

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        return Foundation::make('Antares\Updater\Model\Sandbox')->select(['id', 'version', 'created_at']);
    }

    /**
     * {@inheritdoc}
     */
    public function ajax()
    {
        $acl              = app('antares.acl')->make('antares/updater');
        $canSandboxRun    = $acl->can('sandbox-run');
        $canSandboxDelete = $acl->can('sandbox-delete');
        return $this->prepare()
                        ->addColumn('action', $this->getActionsColumn($eloquent         = null, $canSandboxRun, $canSandboxDelete))
                        ->make(true);
    }

    /**
     * {@inheritdoc}
     */
    public function html()
    {
        return $this->setName('Sandboxes List')
                        ->addColumn(['data' => 'id', 'name' => 'id', 'title' => trans('Id')])
                        ->addColumn(['data' => 'version', 'name' => 'version', 'title' => trans('Version')])
                        ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => trans('Created date')])
                        ->addAction(['name' => 'edit', 'title' => ''])
                        ->setDeferedData();
    }

    /**
     * Get actions column for table builder.
     * @return callable
     */
    protected function getActionsColumn($eloquent, $canSandboxRun, $canSandboxDelete)
    {
        return function ($row) use($canSandboxRun, $canSandboxDelete) {

            $btns = [];
            $html = app('html');
            if ($canSandboxRun) {
                $btns[] = $html->create('li', $html->link(handles("antares::updater/update", ['sandbox' => $row->version, 'csrf' => true]), trans('Launch'), ['data-icon' => 'caret-right-circle']));
            }
            if ($canSandboxDelete) {
                $btns[] = $html->create('li', $html->link(handles("antares::updater/sandbox/delete/" . $row->id, ['csrf' => true]), trans('antares/updater::messages.sandbox.delete'), ['class'            => "triggerable confirm", 'data-icon'        => 'delete',
                            'data-title'       => trans("antares/updater::messages.sandbox.delete_are_you_sure"),
                            'data-description' => trans('antares/updater::messages.sandbox.delete_sandbox_description', ['version' => $row->version])]));
            }
            if (empty($btns)) {
                return '';
            }
            $section = $html->create('div', $html->create('section', $html->create('ul', $html->raw(implode('', $btns)))), ['class' => 'mass-actions-menu'])->get();
            return '<i class="zmdi zmdi-more"></i>' . $html->raw($section)->get();
        };
    }

}
