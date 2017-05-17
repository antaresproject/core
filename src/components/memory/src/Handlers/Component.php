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

namespace Antares\Memory\Handlers;

use Antares\Memory\Exception\ComponentNotSavedException;
use Antares\Memory\Component\ComponentHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function str_contains;
use Exception;

class Component extends ComponentHandler
{

    /**
     * Storage name.
     * @var string
     */
    protected $storage = 'component';

    /**
     * Memory configuration.
     * @var array
     */
    protected $config = [
        'cache' => false,
    ];

    /**
     * updates module permission
     * 
     * @param type $key
     * @param type $value
     * @param type $isNew
     * @return boolean
     * @throws \Exception
     */
    protected function save($key, $value, $isNew = false, $brandId = false, $flag = null)
    {
        try {
            DB::transaction(function () use ($key, $value, $isNew, $brandId, $flag) {
                $name = str_replace('acl_antares/', '', $key);
                if (str_contains($key, 'acl_antares')) {

                    $this->resolver()->updatePermissions($name, $value, $isNew, $brandId);
                } elseif ($isNew) {
                    $name = isset($value['name']) ? $value['name'] : $name;

                    $model = $this->resolver()->component()->getModel()->newInstance();
                    $first = $model->query()->where('name', $name)->get()->first();

                    if (!is_null($first) and $first->exists) {
                        $model = $first;
                    }

                    $model->fill($value + ['status' => ($flag === 'active')]);
                    if (!$model->save()) {
                        throw new ComponentNotSavedException('Unable to save primary module configuration');
                    }
                }
            });
        } catch (Exception $e) {
            Log::emergency($e);
            throw new ComponentNotSavedException($e);
        }
    }

    /**
     * updates elements in container
     * @param array $items
     * @return parent::update
     */
    public function update(array $items = [])
    {
        return parent::update($items);
    }

}
