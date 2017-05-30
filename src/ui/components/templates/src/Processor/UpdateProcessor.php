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

use Antares\UI\UIComponents\Http\Presenters\UpdatePresenter as Presenter;
use Antares\UI\UIComponents\Model\ComponentParams as Eloquent;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Antares\UI\UIComponents\Contracts\Updater as Listener;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Response;

class UpdateProcessor extends Processor
{

    /**
     * Construct
     * 
     * @param Presenter $presenter
     */
    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * Shows create form
     * 
     * @param Listener $listener
     * @return mixed
     */
    public function edit(Listener $listener, $id)
    {
        $eloquent = with(new Eloquent())->findOrNew($id);
        $name     = $eloquent->name;
        $raw      = app('antares.memory')->make('ui-components')->raw();
        $data     = isset($raw[$name]['data']) ? $raw[$name]['data'] : [];
        if (!empty($data)) {
            foreach ($data as $attribute => $value) {
                $eloquent->{$attribute} = $value;
            }
        }
        $form = $this->presenter->form($name, $eloquent, "antares::ui-components/updater");
        return $listener->showComponentUpdater($id, compact('form'));
    }

    /**
     * Updates ui component attributes
     * 
     * @param Listener $listener
     * @param mixed $id
     * @param array $input
     * @return Response
     */
    public function update(Listener $listener, $id, array $input)
    {
        $model     = with(new Eloquent())->findOrNew($id);
        $memory    = app('antares.memory')->make('ui-components');
        $className = $memory->get($model->name . '.name');
        $component = with(new $className($model->id));
        $rules     = $component->getRules();
        if (empty($rules)) {
            return Response::json([], 200);
        }
        $validation = ValidatorFacade::make($input, $rules);


        if ($validation->fails()) {
            if (app('request')->ajax()) {
                return Response::json($validation->getMessageBag()->getMessages(), 200);
            }
            return $listener->whenValidationFailed($validation->getMessageBag());
        } else {
            $controls      = $component->controls();
            $value         = unserialize($model->value);
            $value['data'] = array_only($input, $controls);
            $model->value  = serialize($value);
            $model->save();
            $memory->getHandler()->forgetCache($this->getResource());
            return Response::json([], 200);
        }
    }

    /**
     * Gets resource 
     * 
     * @return String
     */
    protected function getResource()
    {
        $resource = str_replace(url()->to(''), '', app('request')->server->get('HTTP_REFERER'));
        return str_slug($resource);
    }

}
