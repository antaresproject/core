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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Customfields\Events;

use Antares\Contracts\Html\Form\Builder as FormBuilder;

class FormReadyHandler
{

    /**
     * event handler on view form with customfields
     * 
     * @param FormBuilder $formBuilder
     * @return boolean
     */
    public function handle(FormBuilder $formBuilder)
    {
        $grid      = $formBuilder->grid;
        $fieldsets = $grid->fieldsets();
        if (empty($fieldsets)) {
            return false;
        }
        if (!isset($grid->name) or is_null($grid->name)) {
            return false;
        }

        $extension = app('antares.extension')->getActualExtension();
        $memory    = app('antares.memory')->make('registry');
        $namespace = $extension . '.' . $grid->name;
        if (is_null($memory->get($namespace))) {
            $memory->push($namespace, $namespace);
            $memory->finish();
            return false;
        }

        /* @var $formHandler FormHandler */
        $formHandler = app('Antares\Customfields\Events\FormHandler');
        $formHandler->onViewForm($grid->row(), $formBuilder, $namespace);
    }

}
