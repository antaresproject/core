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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Adapter;

use Antares\Html\Form\Grid;
use Antares\Control\Contracts\ControlsAdapter as AdapterContract;
use Antares\Contracts\Html\Form\Fieldset;
use Illuminate\Support\Fluent;

class ControlsAdapter implements AdapterContract
{

    /**
     * adaptee controls on form instance
     * 
     * @param FormFactory $form
     * @param array $controls
     * @param Fluent $model
     * @return boolean
     */
    public function adaptee(Grid &$grid, array $controls = array(), Fluent $model)
    {
        if (empty($controls)) {
            return false;
        }
        $grid->fieldset(function (Fieldset $fieldset) use($controls, $model) {
            $editable    = $model->get('editable', []);
            $displayable = $model->get('displayable', []);

            foreach ($controls as $control) {
                $name = $control['name'];

                $fieldset->control($control['type'], $name)
                        ->label($control['label'])
                        ->value($control['value'])
                        ->attributes(['disabled' => 'disabled', 'readonly' => 'readonly']);

                $fieldset->control('input:checkbox', 'editable[' . $name . ']')
                        ->value($control['value'])
                        ->label(trans('editable'))
                        ->attributes($this->checked($control, $editable));

                $fieldset->control('input:checkbox', 'displayable[' . $name . ']')
                        ->value($control['value'])
                        ->label(trans('displayable'))
                        ->attributes($this->checked($control, $displayable));
            }
        });
    }

    /**
     * verify whether checkbox should be checked
     * 
     * @param array $control
     * @param array $displayable
     * @return string
     */
    protected function checked(array $control, array $displayable)
    {
        if (empty($displayable)) {
            return ['checked' => 'checked'];
        }
        foreach ($displayable as $item) {
            if ($control['name'] == $item['name']) {
                return ['checked' => 'checked'];
            }
        }
        return [];
    }

}
