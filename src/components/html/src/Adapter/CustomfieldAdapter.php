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

namespace Antares\Html\Adapter;

use Antares\Contracts\Html\Form\Fieldset;
use Antares\Customfields\Model\FieldView;
use Antares\Customfield\CustomField;
use Antares\Html\Form\Grid;

class CustomfieldAdapter
{

    /**
     * Grid instance
     *
     * @var Grid 
     */
    protected $grid;

    /**
     * Adapts customfields to form grid
     * 
     * @param Grid $grid
     */
    public function adapt(Grid &$grid)
    {
        $this->grid   = $grid;
        $customfields = app('customfields')->get();
        if (empty($customfields)) {
            return;
        }
        foreach ($customfields as $classname => $customfield) {
            if (!$grid->row instanceof $classname) {
                continue;
            }
            if (!is_array($customfield)) {
                $customfield = [$customfield];
            }

            foreach ($customfield as $instance) {
                if (!is_object($instance)) {
                    continue;
                }
                $this->addToForm($instance);
            }
        }

        if (extension_active('customfields')) {
            $map   = config('antares/customfields::map', []);
            $types = [];
            foreach ($map as $type => $classnames) {
                foreach ($classnames as $classname) {
                    if (!$this->grid->row instanceof $classname) {
                        continue;
                    }
                    array_push($types, $type);
                }
            }
            $this->getFields($types);
        }
    }

    /**
     * Assigns customfield to form
     * 
     * @param CustomField $customfield
     * @return $this
     */
    protected function addToForm(CustomField $customfield)
    {
        if (!$customfield->formAutoDisplay()) {
            return false;
        }
        $field = $customfield->setModel($this->grid->row);


        $this->grid->fieldset(function (Fieldset $fieldset) use($field) {
            $fieldset->add($field);
        });
        $this->grid->rules(array_merge($this->grid->rules, $field->getRules()));
        $this->grid->row->saved(function($row) use($field) {
            $field->onSave($row);
        });



        return $this;
    }

    protected function getFields(array $types = [])
    {
        $fields = FieldView::query()->whereIn('category_name', $types)->where(['imported' => 0, 'force_display' => 1])->get();

        foreach ($fields as $field) {
            if (is_null($field)) {
                continue;
            }
            $customfield = new \Antares\Customfield\CustomField();
            $customfield = $customfield->attributes($field);
            $customfield->setModel($this->grid->row);

            $this->grid->fieldset(function (Fieldset $fieldset) use($customfield) {
                $fieldset->add($customfield);
            });
            $this->grid->rules(array_merge($this->grid->rules, $customfield->getRules()));

            $this->grid->row->saved(function($row) use($customfield) {
                $customfield->onSave($row);
            });
        }
    }

}
