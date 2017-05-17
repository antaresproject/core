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


namespace Antares\Support;

use Illuminate\Support\Arr;
use Antares\Support\Contracts\CsvableInterface;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection implements CsvableInterface
{

    /**
     * {@inheritdoc}
     */
    public function toCsv()
    {
        $delimiter = ',';
        $enclosure = '"';
        $header    = $this->resolveCsvHeader();

        ob_start();

        $instance = fopen('php://output', 'r+');

        fputcsv($instance, $header, $delimiter, $enclosure);

        foreach ($this->items as $key => $item) {
            fputcsv($instance, Arr::dot($item), $delimiter, $enclosure);
        }

        return ob_get_clean();
    }

    /**
     * Resolve CSV header.
     *
     * @return array
     */
    protected function resolveCsvHeader()
    {
        $header = [];

        if (!$this->isEmpty()) {
            $single = $this->first();
            $header = array_keys(Arr::dot($single));
        }

        return $header;
    }

    /**
     * find elements except elements which has field founded in keys
     * 
     * @param String $field
     * @param array $keys
     * @return Collection
     */
    public function except_by_key($field, $keys)
    {
        return $this->filter(function($fieldset) use($field, $keys) {
                    if (isset($fieldset->{$field}) and in_array($fieldset->{$field}, $keys)) {
                        return false;
                    }
                });
    }

    /**
     * finds element in collection by element fieldname and field value
     * 
     * @param String $field
     * @param mixed $value
     * @return mixed
     */
    public function by($field, $value)
    {
        $filtered = $this->filter(function($fieldset) use($field, $value) {
            if (isset($fieldset->{$field}) and $fieldset->{$field} == $value) {
                return true;
            }
            return false;
        });
        return $filtered->isEmpty() ? null : ($filtered->count() > 1 ? $filtered : $filtered->first());
    }

}
