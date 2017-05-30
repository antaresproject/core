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

namespace Antares\Model;

use Antares\Security\Traits\DbCryptTrait as DatabaseCryptor;
use Antares\Customfield\Traits\Customfields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Closure;

abstract class Eloquent extends Model
{

    use DatabaseCryptor,
        Customfields;

    /**
     * Determine if the model instance uses soft deletes.
     *
     * @return bool
     */
    public function isSoftDeleting()
    {
        return (property_exists($this, 'forceDeleting') && $this->forceDeleting === false);
    }

    /**
     * {@inheritdoc}
     */
    public function find($id, $columns = array('*'))
    {
        Event::fire('before.find', [new static]);
        $result = parent::find($id, $columns);
        Event::fire('after.find', array($result));
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail($id, $columns = array('*'))
    {
        Event::fire('before.find', [new static]);
        $result = parent::findOrFail($id, $columns);
        Event::fire('after.find', array($result));
        return $result;
    }

    /**
     * Call static for log url pattern getter
     * 
     * @param mixed $method
     * @param mixed $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        if ($method === 'getPatternUrl') {
            return static::getUrlPattern();
        }
        return parent::__callStatic($method, $parameters);
    }

    /**
     * Get dependable actions
     * 
     * @return array
     */
    public function dependableActions()
    {
        $actions = config('dependable_actions.' . get_called_class(), []);
        $return  = [];
        foreach ($actions as $action) {
            if (!$action instanceof Closure) {
                continue;
            }
            $return[] = call_user_func($action, $this);
        }

        return $return;
    }

    /**
     * Gets log title
     * 
     * @param mixed $id
     * @param Model $model
     * @return boolean
     */
    public static function getLogTitle($id, $model)
    {
        return false;
    }

}
