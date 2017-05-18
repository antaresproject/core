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

namespace Antares\Html\Validation;

use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Antares\Html\Validation\SupportValidator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class Validator implements ValidatorContract
{

    /**
     * form grid instance
     *
     * @var \Antares\Html\Form\Grid
     */
    protected $grid;

    /**
     * @var Request
     */
    protected $request;

    /**
     * message bag container
     *
     * @var \Antares\Messages\MessageBag
     */
    protected $messageBag;

    /**
     * custom fields validator configuration
     * 
     * @var array 
     */
    protected $customFieldsValidator;
    protected $supportValidator;

    /**
     * constructing
     * 
     * @param Request $request
     */
    public function __construct(Request $request, SupportValidator $supportValidator)
    {
        $this->request          = $request;
        $this->supportValidator = $supportValidator;
    }

    /**
     * 
     * @param type $grid
     * @return \Antares\Html\Validation\Validator
     */
    public function with($grid)
    {
        $this->grid = $grid;
        return $this;
    }

    /**
     * custom fields validator configuration setter
     * 
     * @param array $customfieldsValidator
     * @return \Antares\Html\Validation\Validator
     */
    public function withCustomFields(array $customfieldsValidator = array())
    {
        $this->customFieldsValidator = $customfieldsValidator;
        return $this;
    }

    /**
     * validate form
     * 
     * @return mixed
     */
    public function validate($sendHeaders = true)
    {
        app('events')->fire('antares.form: validate', $this->grid);
        $inputs          = Input::all();
        $messages        = null;
        $customValidator = !is_null($this->grid->customValidator) ? $this->grid->customValidator : null;
        if (!is_null($customValidator)) {
            if ($customValidator->fails()) {
                $messages = $customValidator->getMessageBag();
            }
        }
        $rules = $this->grid->rules;

        if (!empty($rules)) {
            $validation = ValidatorFacade::make($inputs, $rules, $this->grid->phrases);
            if ($validation->fails()) {
                $messages = $this->uniqueMessages($validation->getMessageBag(), $messages);
            }
        }
        return $this->whenValidated($messages, $sendHeaders);
    }

    /**
     * unify message bugs from custom validators and from form
     * 
     * @param \Illuminate\Support\MessageBag $messageBag
     * @param \Illuminate\Support\MessageBag $messageBagFromCustom
     * @return \Illuminate\Support\MessageBag
     */
    protected function uniqueMessages($messageBag, $messageBagFromCustom = null)
    {
        if (is_null($messageBagFromCustom)) {
            return $messageBag;
        }
        $messages = $messageBagFromCustom->getMessages();
        foreach ($messages as $key => $value) {
            if (empty($value)) {
                continue;
            }
            foreach ($value as $messageName) {
                if ($messageBag->has($key, $messageName)) {
                    continue;
                }
                $messageBag->add($key, $messageName);
            }
        }
        return $messageBag;
    }

    /**
     * after form validation
     *
     * @param $messageBag
     * @param $sendHeaders
     * @return bool
     */
    protected function whenValidated($messageBag, $sendHeaders)
    {
        $messages = empty($messageBag) ? [] : $messageBag->getMessages();
        if ($this->request->ajax() && $sendHeaders) {
            $return = [];
            foreach ($messages as $key => $phrases) {
                if (str_contains($key, '[]')) {
                    $key = str_replace(['[', ']'], '_', $key);
                }
                $return[$key] = $phrases;
            }
            return $this->response($return);
        }
        if (!empty($messages)) {
            $this->messageBag = $messageBag;
            return false;
        }
        return true;
    }

    /**
     * send proper headers and terminate application
     *
     * @param array $messages
     * @return bool
     */
    protected function response(array $messages = [])
    {
        $response = Response::json($messages, 200);
        $response->sendHeaders();
        $response->sendContent();
        app()->terminate();
        exit;
    }

    /**
     * get message bag from container
     * 
     * @return \Antares\Messages\MessageBag
     */
    public function getMessageBag()
    {
        return $this->messageBag;
    }

}
