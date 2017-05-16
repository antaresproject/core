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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Automation\Repository;

use Antares\Foundation\Repository\AbstractRepository;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\DB;
use Exception;

class Reports extends AbstractRepository
{

    /**
     * name of repositroy model
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        return config('antares/automation::memory.model');
    }

    /**
     * saves scheduler actions
     * 
     * @param String $commandName
     * @param String $runtime
     * @param Process $process
     * @return boolean
     */
    public function saveReport($commandName, $runtime, Process $process)
    {
        $this->makeModel();
        $model         = $this->model->where('name', $commandName)->first();
        $processFailed = !$process->isSuccessful();
        if (is_null($model)) {
            return false;
        }
        $hasError = false;
        DB::beginTransaction();
        try {
            $result = $model->jobResults()->getModel()->newInstance();
            $result->fill([
                'job_id'    => $model->id,
                'has_error' => $processFailed,
                'runtime'   => $runtime,
                'return'    => $process->getOutput(),
            ]);
            $result->save();
            if ($processFailed) {
                $error = $result->jobErrors()->getModel()->newInstance();
                $error->insert([
                    'result_job_id' => $result->id,
                    'code'          => $process->getExitCode(),
                    'name'          => $process->getExitCodeText(),
                    'return'        => $process->getErrorOutput(),
                ]);
            }
        } catch (Exception $e) {
            $hasError = true;
        }
        if ($hasError) {
            DB::rollback();
            return false;
        }
        DB::commit();
        return true;
    }

}
