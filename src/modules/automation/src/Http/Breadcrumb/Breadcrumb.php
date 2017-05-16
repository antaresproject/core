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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Automation\Http\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\Facade as Breadcrumbs;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb
{

    /**
     * on init automation
     */
    public function onInit()
    {
        if (!Breadcrumbs::exists('automations')) {
            Breadcrumbs::register('automations', function($breadcrumbs) {
                $breadcrumbs->push(trans('antares/automation::messages.automation_log'), handles('antares::automation/index'));
            });
            view()->share('breadcrumbs', Breadcrumbs::render('automations'));
        }
    }

    /**
     * on edit automation
     * 
     * @param Model $model
     */
    public function onEdit(Model $model)
    {
        $this->onInit();
        $name = $model->exists ? $model->name : 'add';

        Breadcrumbs::register('automation-' . $name, function($breadcrumbs) use($model) {
            $breadcrumbs->parent('automations');
            $name = $model->exists ? 'Automation edit ' . $model->name : 'Automation create';
            $breadcrumbs->push($name);
        });
        view()->share('breadcrumbs', Breadcrumbs::render('automation-' . $name));
    }

    /**
     * on list automations
     */
    public function onList()
    {
        Breadcrumbs::register('automation', function($breadcrumbs) {
            $breadcrumbs->push('Automation', handles('antares::automation/index'));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('automation'));
    }

    /**
     * on shows automation details
     * 
     * @param Model $model
     */
    public function onShow(Model $model)
    {
        $this->onInit();
        Breadcrumbs::register('automation-details-' . $model->id, function($breadcrumbs) use($model) {
            $breadcrumbs->parent('automations');
            $breadcrumbs->push('Automation details ' . $model->name, handles('antares::automation/show/' . $model->id));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('automation-details-' . $model->id));
    }

    /**
     * when automation job completed successfully
     * 
     * @param Model $model
     */
    public function onRunSucceed(Model $model)
    {
        $this->onInit();
        Breadcrumbs::register('automation-result-succeed', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('automations');
            $breadcrumbs->push('Automation job succeed for script ' . $model->name, handles('antares::automation/run/' . $model->id));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('automation-result-succeed'));
    }

    /**
     * when automation job result failed
     * 
     * @param Model $model
     */
    public function onRunFailed(Model $model)
    {
        $this->onInit();
        Breadcrumbs::register('automation-result-failed', function($breadcrumbs) use($model) {
            $breadcrumbs->parent('automations');
            $breadcrumbs->push('Automation job failed for script ' . $model->name, handles('antares::automation/run/' . $model->id));
        });
        view()->share('breadcrumbs', Breadcrumbs::render('automation-result-failed'));
    }

}
