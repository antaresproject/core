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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Tester\Http\Presenters;

use Antares\Contracts\Html\Form\Factory as FormFactory;
use Antares\Contracts\Html\Form\Grid as FormGrid;
use Antares\Foundation\Http\Presenters\Presenter;
use Antares\Tester\Http\Breadcrumb\Breadcrumb;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Contracts\Html\Form\Factory;
use Antares\Tester\Contracts\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Fluent;
use ReflectionClass;
use Exception;

class CollectivePresenter extends Presenter
{

    /**
     * @var Antares\Tester\Contracts\RoundRobinContract 
     */
    protected $roundRobin;

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * constructing a new module tester presenter     
     * @param Antares\Tester\Contracts\RoundRobinContract $roundRobin
     * @param Factory $form
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Builder $roundRobin, FormFactory $form, Breadcrumb $breadcrumb)
    {
        $this->roundRobin = $roundRobin;
        $this->form       = $form;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * script prepare before round robin launch
     * 
     * @return array
     * @throws Exception
     */
    protected function prepare()
    {
        $active = app('antares.memory')->get("extensions.active");
        $memory = app('antares.memory')->make('tests');
        $tests  = $memory->all();
        $return = [];
        foreach ($tests as $index => $data) {
            if (!isset($data['executor'])) {
                $memory->forget($index);
                continue;
            }
            try {
                $reflection = new ReflectionClass($data['executor']);
                if (!$reflection->hasMethod('addTestButton')) {
                    throw new Exception('Form Configuration is invalid. Form with test must contains Testable Trait.');
                }
            } catch (Exception $e) {
                Log::emergency($e);
                $memory->forget($index);
                continue;
            }
            $name                                     = $data['component'];
            $fullName                                 = isset($active[$name]) ? $active[$name]['full_name'] : 'Foundation';
            $return[$fullName][$data['name']][$index] = $data;
        }
        $memory->finish();
        return $return;
    }

    /**
     * form generator
     * 
     * @return \Collective\Html\FormBuilder
     */
    public function form()
    {
        $this->breadcrumb->onForm();
        $all  = $this->prepare();
        $this->roundRobin->build();
        $form = $this->form->of("antares.tester: test", function (FormGrid $form) use ($all) {
            $model = new Fluent([]);
            $form->setup($this, "antares::tools/tester/prepare", $model, ['id' => 'tester-form']);
            $form->name('Module tester');
            foreach ($all as $module => $tests) {
                $form->fieldset($module, function (Fieldset $fieldset) use ($tests) {
                    $fieldset->control('input:text', '', function($control) use($tests) {
                        $control->field = function() use($tests) {
                            return view('antares/tester::admin.partials._module', ['tests' => $tests]);
                        };
                    });
                });
            }
            $form->layout('antares/tester::admin.partials._form');
        });

        $form->grid->submit = trans('Start testing');
        return $form;
    }

}
