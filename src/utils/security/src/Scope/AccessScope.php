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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Security\Scope;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Antares\Model\Role;

class AccessScope implements Scope
{

    /**
     * name of columns which can be used as user connector
     *
     * @var array
     */
    protected static $patternedColumns = [
        'user_id', 'uid'
    ];

    /**
     * apply active logs global scope
     * 
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model)
    {
        $column = $this->getPolicyColumn($model);
        if (is_null($column) && get_class($model) !== \Antares\Model\User::class) {
            return;
        }

        if (!auth()->user()) {
            return;
        }
        $id       = auth()->user()->roles->first()->id;
        $elements = Cache::remember('roles1-' . $id, 5, function() use($id) {
                    $roles = app(Role::class)->withTrashed()->orderby('parent_id')->get()->toArray();
                    return $this->getLowerRoles($roles, $id);
                });

        $uid = user()->id;
        if (get_class($model) === \Antares\Model\User::class) {
            if (empty($elements)) {
                $builder->whereRaw("(tbl_users.id is null or tbl_users.id=?)", [$uid]);
            } else {
                $in = implode(',', array_values($elements));
                $builder->whereRaw("(tbl_users.id is null or tbl_users.id=? or tbl_users.id in (select user_id from tbl_user_role where role_id in({$in})))", [$uid]);
            }
        } elseif (!empty($elements)) {
            $in = implode(',', array_values($elements));
            $builder->whereRaw("(user_id is null or user_id=? or user_id in (select user_id from tbl_user_role where role_id in({$in})))", [$uid]);
        }
    }

    /**
     * get column name which can be used as user connector
     * 
     * @param Model $model
     * @return String
     */
    protected function getPolicyColumn(Model $model)
    {

        $connection = $model->getConnection();
        $table      = $connection->getTablePrefix() . $model->getTable();
        $schema     = $connection->getDoctrineSchemaManager($table);
        $columns    = Cache::remember('keys_' . $table, 30, function() use($model) {
                    return Schema::getColumnListing($model->getTable());
                });


        $column = null;
        foreach (self::$patternedColumns as $name) {
            if (!is_null($column)) {
                break;
            }
            $column = array_search($name, $columns) !== false ? $name : null;
        }
        if (is_null($column)) {
            foreach ($schema->listTableForeignKeys($table) as $foreignKeyConstraint) {
                if ($foreignKeyConstraint->getForeignTableName() !== 'tbl_users') {
                    continue;
                }
                $column = current($foreignKeyConstraint->getLocalColumns());
            }
        }

        return $column;
    }

    /**
     * builds recursive widgets stack
     * 
     * @param array $elements
     * @param mixed $parentId
     * @return array
     */
    protected function getLowerRoles(array $elements, $parentId = 0, &$return = [])
    {
        foreach ($elements as $element) {
            if ($element['parent_id'] != $parentId) {
                continue;
            }
            $children = $this->getLowerRoles($elements, $element['id'], $return);
            if ($children) {
                foreach ($children as $child) {
                    $return[] = $child['id'];
                }
            }
            $return[] = $element['id'];
        }

        return array_filter($return);
    }

}
