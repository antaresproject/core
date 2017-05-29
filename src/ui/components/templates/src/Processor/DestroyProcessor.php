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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents\Processor;

use Antares\UI\UIComponents\Contracts\Destroyer as DestroyListener;
use Antares\UI\UIComponents\Model\ComponentParams;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Log;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Exception;

class DestroyProcessor extends Processor
{

    /**
     * @var Container 
     */
    protected $container;

    /**
     * widgets repository
     * 
     * @var \Antares\UI\UIComponents\Repository\Repository 
     */
    protected $repository;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container  = $container;
        $this->repository = $container->make('ui-components');
    }

    /**
     * Disables ui component
     * 
     * @param numeric $id
     * @return mixed | array
     */
    public function disable(DestroyListener $listener, $id)
    {
        try {
            DB::transaction(function() use($id) {
                $model            = ComponentParams::where('id', $id)->first();
                $data             = $model->data;
                $data['disabled'] = true;
                $model->data      = $data;
                $this->repository->saveEntity($model);
            });

            return $listener->whenDestroySuccess();
        } catch (Exception $e) {
            Log::emergency($e);
            return $listener->whenDestroyError(['message' => $e->getMessage(), 'code' => $e->getCode()]);
        }
    }

}
