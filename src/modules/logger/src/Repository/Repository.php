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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Repository;

use Antares\Foundation\Repository\AbstractRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Antares\Logger\Model\Logs;

class Repository extends AbstractRepository
{

    /**
     * name of repositroy model
     * 
     * @return Logs
     */
    public function model()
    {
        return Logs::class;
    }

    /**
     * find models by type
     * 
     * @param mixed $componentId
     * @return \Illuminate\Support\Collection
     */
    public function findByAttributes($componentId = null)
    {
        return $this->model->with('component', 'priority', 'user', 'brand')->whereHas('component', function($q) use($componentId) {
                    $q->where('active', 1);
                    if (!is_null($componentId)) {
                        $q->where('id', $componentId);
                    }
                })->where('brand_id', brand_id())->orderBy('created_at', 'desc');
    }

    /**
     * find rows by component type name
     * 
     * @param String $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findByComponentName($name = null)
    {
        return $this->model->with('component', 'priority', 'brand', 'jobs')->whereHas('component', function($q) use($name) {
                    $q->where('active', 1);
                    if (!is_null($name)) {
                        $q->where('name', $name);
                    }
                })->where('brand_id', brand_id());
    }

    /**
     * Finds all logs by user id
     * 
     * @param mixed $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findByUser($userId = null)
    {
        $builder = $this->model->select(['tbl_logs.id', 'tbl_logs.priority_id', 'tbl_logs.type_id', 'tbl_logs.brand_id', 'tbl_logs.user_id', 'tbl_logs.owner_type', 'tbl_logs.owner_id', 'tbl_logs.old_value', 'tbl_logs.new_value', 'tbl_logs.related_data', 'tbl_logs.type', 'tbl_logs.name', 'tbl_logs.created_at'])
                ->with('component', 'priority', 'brand')
                ->whereHas('user', function($q) use($userId) {
                    if (!is_null($userId)) {
                        $q->where('user_id', $userId);
                    }
                })->whereHas('component', function($q) {
                    $q->where('active', 1);
                })
                ->where('brand_id', brand_id())
                ->orderBy('created_at', 'desc');
        $builder->withoutGlobalScopes();
        return $builder;
    }

    /**
     * Deletes logs using range
     * 
     * @param String $from
     * @param String $to
     * @return boolean
     */
    public function deleteByRange($from, $to)
    {
        DB::beginTransaction();
        try {
            $from = $from . ' 00:00:00';
            $to   = $to . ' 23:59:59';
            $logs = Logs::whereBetween('created_at', [$from, $to]);
            $logs->delete();
        } catch (Exception $ex) {
            Log::alert($ex);
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

}
