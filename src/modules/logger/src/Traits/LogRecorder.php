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

namespace Antares\Logger\Traits;

use Antares\Logger\Model\LogPriorities;
use Illuminate\Support\Facades\Request;
use Antares\Logger\RelationResolver;
use Illuminate\Support\Facades\Log;
use Antares\Logger\Model\LogTypes;
use Antares\Logger\Model\Logs;
use ReflectionClass;
use Exception;
use DateTime;
use Auth;

trait LogRecorder
{

    /**
     * @var array
     */
    private $originalData = [];

    /**
     * @var array
     */
    private $updatedData = [];

    /**
     * @var array
     */
    private $dontKeep = [];

    /**
     * @var array
     */
    private $doKeep = [];

    /**
     * @var bool
     */
    private $updating = false;

    /**
     * @var array
     */
    protected $dirtyData = [];

    /**
     * Init auditing.
     */
    public static function bootLogRecorder()
    {
        static::saving(function ($model) {
            $model->prepareAudit();
        });

        static::created(function ($model) {
            if ($model->isTypeAuditable('created')) {
                $model->auditCreation();
            }
        });

        static::saved(function ($model) {
            if ($model->isTypeAuditable('saved')) {
                $model->auditUpdate();
            }
        });

        static::deleted(function ($model) {
            if ($model->isTypeAuditable('deleted')) {
                $model->prepareAudit();
                $model->auditDeletion();
            }
        });
    }

    /**
     * Get list of logs.
     *
     * @return mixed
     */
    public function logs()
    {
        return $this->morphMany(Logs::class, 'owner');
    }

    /**
     * Generates a list of the last $limit revisions made to any objects
     * of the class it is being called from.
     *
     * @param int    $limit
     * @param string $order
     *
     * @return mixed
     */
    public static function classLogHistory($limit = 100, $order = 'desc')
    {
        try {
            return Logs::where('owner_type', get_called_class())->orderBy('updated_at', $order)->limit($limit)->get();
        } catch (Exception $ex) {
            
        }
    }

    /**
     * @param int    $limit
     * @param string $order
     *
     * @return mixed
     */
    public function logHistory($limit = 100, $order = 'desc')
    {
        return static::classLogHistory($limit, $order);
    }

    /**
     * Prepare audit model.
     */
    public function prepareAudit()
    {
        if (!isset($this->auditEnabled) || $this->auditEnabled) {
            $this->originalData = $this->original;
            $this->updatedData  = $this->attributes;

            foreach ($this->updatedData as $key => $val) {
                if (gettype($val) == 'object' && !method_exists($val, '__toString')) {
                    unset($this->originalData[$key]);
                    unset($this->updatedData[$key]);
                    array_push($this->dontKeep, $key);
                }
            }

            // Dont keep log of
            $this->dontKeep = isset($this->dontKeepLogOf) ?
                    $this->dontKeepLogOf + $this->dontKeep : $this->dontKeep;

            // Keep log of
            $this->doKeep = isset($this->keepLogOf) ?
                    $this->keepLogOf + $this->doKeep : $this->doKeep;

            unset($this->attributes['dontKeepLogOf']);
            unset($this->attributes['keepLogOf']);


            // Get changed data
            $this->dirtyData = $this->getDirty();
            // Tells whether the record exists in the database
            $this->updating  = $this->exists;
        }
    }

    /**
     * Audit creation.
     */
    public function auditCreation()
    {
        if ((!isset($this->auditEnabled) || $this->auditEnabled)) {
            $insert = array_merge([
                'old_value'    => null,
                'author_id'    => auth()->guest() ? null : auth()->user()->id,
                'new_value'    => $this->getAttributes(),
                'related_data' => $this->getRelatedData(),
                'type'         => 'created',], $this->values());

            return $this->audit($insert);
        }
    }

    /**
     * Related data getter
     * 
     * @return array
     */
    protected function getRelatedData()
    {
        return app(RelationResolver::class)->getRelationData($this);
    }

    /**
     * Audit updated.
     */
    public function auditUpdate()
    {
        try {
            $history = $this->logHistory();
            if (is_null($history)) {
                return false;
            }
            if (isset($this->historyLimit) && $this->logHistory()->count() >= $this->historyLimit) {
                $LimitReached = true;
            } else {
                $LimitReached = false;
            }
            if (isset($this->logCleanup)) {
                $LogCleanup = $this->LogCleanup;
            } else {
                $LogCleanup = false;
            }

            if (((!isset($this->auditEnabled) || $this->auditEnabled) && $this->updating) && (!$LimitReached || $LogCleanup)) {
                $changes_to_record = $this->changedAuditingFields();
                if (count($changes_to_record)) {
                    $log = ['type' => 'updated'];
                    foreach ($changes_to_record as $key => $change) {
                        $log['old_value'][$key] = array_get($this->originalData, $key);
                        $log['new_value'][$key] = array_get($this->updatedData, $key);
                    }

                    $log['related_data'] = $this->getRelatedData();
                    $this->audit($log);
                }
            }
        } catch (Exception $ex) {
            Log::emergency($ex);
            return false;
        }
    }

    /**
     * get constantiable values 
     * 
     * @return array
     */
    protected function values()
    {
        try {
            return [
                'type_id'     => $this->resolveTypeIdByOwner(),
                'priority_id' => $this->getPriorityId(),
                'brand_id'    => brand_id(),
                'owner_type'  => get_class($this),
                'owner_id'    => $this->getKey(),
                'user_id'     => $this->getUserId(),
                'route'       => str_replace(app('url')->to('/'), '', Request::url()),
                'ip_address'  => Request::getClientIp(),
                'user_agent'  => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'No UserAgent',
                'created_at'  => new DateTime(),
                'updated_at'  => new DateTime(),
            ];
        } catch (Exception $ex) {
            return [];
        }
    }

    /**
     * priority id getter
     * 
     * @return String
     * @throws Exception
     */
    protected function getPriorityId()
    {
        $name     = !$this->priority ? 'medium' : $this->priority;
        $priority = LogPriorities::where('name', $name)->first();
        if (is_null($priority)) {
            throw new Exception(sprintf("Unable to find log priority %s.", $name));
        }
        return $priority->id;
    }

    /**
     * current component name resolver
     * 
     * @return String
     * @throws Exception
     */
    protected function resolveTypeIdByOwner()
    {
        try {
            if (method_exists($this, 'getLogTypeId')) {
                return self::getLogTypeId();
            }

            $reflection = new ReflectionClass($this);

            $filename = $reflection->getFileName();
            $match    = str_contains($filename, 'app') ? [1 => 'core'] : null;

            if (!isset($match[1]) and ! preg_match("'src(.*?)src'si", $filename, $match)) {
                throw new Exception('Unable to resolve current module name.');
            }

            if (!isset($match[1])) {
                throw new Exception('Unable to resolve current module namespace.');
            }

            $reserved = [
                'components', 'modules'
            ];
            $module   = (str_contains($match[1], 'core')) ? 'core' : trim(str_replace($reserved, '', $match[1]), DIRECTORY_SEPARATOR);
            $type     = LogTypes::where('name', $module)->first();
            if (is_null($type)) {
                $type = new LogTypes(['name' => $module]);
                $type->save();
            }
            return $type->id;
        } catch (Exception $ex) {
            Log::emergency($ex);
        }
    }

    /**
     * Audit deletion.
     */
    public function auditDeletion()
    {
        if ((!isset($this->auditEnabled) || $this->auditEnabled)) {
            $type   = 'deleted';
            $delete = array_merge([
                'old_value'    => $this->getAttributes(),
                'new_value'    => null,
                'related_data' => $this->getRelatedData(),
                'type'         => $type], $this->values());
            return $this->audit($delete);
        }
    }

    /**
     * resolving operation name
     * 
     * @param String $type
     * @return String
     */
    protected function resolveOperationName($type)
    {
        return strtoupper(implode('_', [last(explode('\\', get_class($this))), $type]));
    }

    /**
     * Audit model.
     */
    public function audit(array $log)
    {
        try {
            $type     = $log['type'];
            $values   = $this->values();
            $authorId = array_get($log, 'author_id');
            if (is_null($authorId) && isset($values['owner_type'])) {
                $created = Logs::withoutGlobalScopes()->where([
                            'owner_type' => $values['owner_type'],
                            'type'       => 'created',
                            'owner_id'   => $values['owner_id']
                        ])->whereNotNull('author_id')->first();
                if (!is_null($created)) {
                    $authorId = $created->author_id;
                }
            }

            $logAuditing = array_merge([
                'author_id'      => $authorId,
                'old_value'      => $log['old_value'],
                'new_value'      => $log['new_value'],
                'related_data'   => $log['related_data'],
                'name'           => $this->resolveOperationName($type),
                'is_api_request' => is_api_request(),
                'type'           => $type], $values);

            $log             = new Logs($logAuditing);
            $log->created_at = (method_exists($this, 'createdAt')) ? $this->createdAt() : new DateTime();

            if (!$log->save()) {
                throw new Exception('Unable to save log entity.');
            }
        } catch (Exception $ex) {
            Log::emergency($ex);
            return false;
        }
        return true;
    }

    /**
     * Get user id.
     *
     * @return null
     */
    protected function getUserId()
    {
        try {
            if (Auth::check()) {
                return Auth::user()->getAuthIdentifier();
            }
        } catch (Exception $e) {
            Log::emergency($e);
            return;
        }

        return;
    }

    /**
     * Fields Changed.
     *
     * @return array
     */
    private function changedAuditingFields()
    {
        $changes_to_record = [];
        $attributes        = $this->getAttributes();
        foreach ($attributes as $key => $value) {

            if ($this->isAuditing($key) && !is_array($value)) {
                // Check whether the current value is difetente the original value
                //if (!isset($this->originalData[$key]) || $this->originalData[$key] != $this->updatedData[$key]) {
                $changes_to_record[$key] = $value;
                //}
            } else {
                unset($this->updatedData[$key]);
                unset($this->originalData[$key]);
            }
        }

        return $changes_to_record;
    }

    /**
     * Is Auditing?
     *
     * @param $key
     *
     * @return bool
     */
    private function isAuditing($key)
    {
        // Checks if the field is in the collection of auditable
        if (isset($this->doKeep) && in_array($key, $this->doKeep)) {
            return true;
        }

        // Checks if the field is in the collection of non-auditable
        if (isset($this->dontKeep) && in_array($key, $this->dontKeep)) {
            return false;
        }

        // Checks whether the auditable list is clean
        return empty($this->doKeep);
    }

    /**
     * Idenfiable name.
     *
     * @return mixed
     */
    public function identifiableName()
    {

        return $this->getKey();
    }

    /**
     * Verify is type auditable.
     *
     * @param $key
     *
     * @return bool
     */
    public function isTypeAuditable($key)
    {
        $auditableTypes = isset($this->auditableTypes) ? $this->auditableTypes : ['created', 'saved', 'deleted'];

        // Checks if the type is in the collection of type-auditable
        if (in_array($key, $auditableTypes)) {
            return true;
        }

        return;
    }

}
