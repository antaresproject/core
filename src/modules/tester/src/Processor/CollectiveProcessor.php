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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Tester\Processor;

use Antares\Foundation\Processor\Processor;
use Antares\Tester\Http\Presenters\CollectivePresenter as Presenter;
use Antares\Tester\Contracts\TesterProcess as ProcessListener;
use Antares\Tester\Contracts\TesterView as ViewListener;
use Illuminate\Support\Facades\Response;
use Antares\Tester\Exception;
use Antares\Tester\Model\MemoryTests;
use Illuminate\Support\Facades\Log;

class CollectiveProcessor extends Processor
{

    /**
     * constructing
     *
     * @param Presenter $presenter            
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * shows module tester default index action
     *
     * @return Antares\Tester\Contracts\TesterView
     */
    public function index(ViewListener $listener)
    {
        $form = $this->presenter->form();
        return $listener->show(compact('form'));
    }

    /**
     * build sepcification data before real test
     *
     * @param array $data            
     */
    public function prepare(array $data = null)
    {
        $response = [];
        $code     = 200;
        try {
            if (!isset($data['module']) or empty($data['module'])) {
                throw new Exception\InvalidArgumentException(
                'Unable to start tests. No module has been selected.');
            }

            $selected = array_keys($data['module']);

            $tests  = app('antares.memory')->make('tests')->all();
            $active = app('antares.memory')->get("extensions.active");
            foreach ($tests as $name => $test) {

                if (in_array($test['id'], $selected)) {
                    $fullName   = isset($active[$test['component']]) ? $active[$test['component']]['full_name'] : 'Foundation';
                    $response[] = [
                        'cid'     => $test['id'],
                        'message' => "Currently testing {$name}  in {$fullName}."
                    ];
                }
            }
        } catch (\Exception $ex) {
            Log::warning($ex);
            $code       = 400;
            $response[] = [
                'error' => $ex->getMessage()
            ];
        }

        return Response::json($response, $code);
    }

    /**
     * run validation of partial module configuration
     *
     * @param array $data            
     * @return Illuminate\Support\Facades\Response
     */
    public function run(ProcessListener $listener, array $data)
    {
        try {
            if (empty($data)) {
                throw new Exception\InvalidArgumentException('Invalid method arguments.');
            }
            $validator = null;
            $values    = null;
            if (isset($data['cid'])) {
                $model     = MemoryTests::findOrNew($data['cid']);
                $params    = unserialize($model->value);
                $validator = new $params['validator'];
                $values    = !empty($params['controls']) ? $params['controls'] : array_get($params, 'data');
            }
            if (isset($data['validator'])) {
                $validator = new $data['validator'];
                $values    = $data;
            }
            $response = $validator($values)->getResponse();
            $return   = view('antares/tester::admin.partials._messages')->with(['response' => $response]);
        } catch (\Exception $e) {
            Log::emergency($e);
            $return = view('antares/tester::admin.partials._error')->with(['message' => $e->getMessage(), 'code' => $e->getCode()]);
        }

        return $listener->render($return);
    }

}
