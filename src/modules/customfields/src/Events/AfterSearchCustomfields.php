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

use Antares\Customfields\Model\FieldView;

class AfterSearchCustomfields
{

    /**
     * Handles event
     * 
     * @param array $customfields
     */
    public function handle(array $customfields = [])
    {
        if (empty($customfields)) {
            return;
        }
        /**
         * Taking all customfields from db
         */
        $fields = FieldView::query()->with('fieldsets')->where('brand_id', brand_id())->get()->groupBy('category_name');

        /**
         * Traverse customfields to fill database configuration
         */
        foreach ($customfields as $classname => $customfield) {
            $slug  = strtolower(last(explode('\\', $classname)));
            $group = $fields->get($slug, []);
            foreach ($customfield as $field) {
                if (empty($group)) {
                    continue;
                }
                $first = $group->first(function ($value, $key) use($field) {
                    return $value->name === $field->getName();
                });

                $field->attach($first);
            }
        }
    }

}
