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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Notifications\Processor;

use Antares\Notifications\Contracts\IndexPresenter as Presenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Antares\Notifications\Adapter\VariablesAdapter;
use Antares\Notifications\Contracts\IndexListener;
use Antares\View\Notification\NotificationHandler;
use Antares\Notifications\Repository\Repository;
use Antares\Foundation\Processor\Processor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Input;
use Antares\Notifier\NotifiableTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class IndexProcessor extends Processor
{

    use NotifiableTrait;

    /**
     * instance of variables adapter
     *
     * @var VariablesAdapter
     */
    protected $variablesAdapter;

    /**
     * repository instance
     *
     * @var Repository
     */
    protected $repository;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param VariablesAdapter $adapter
     * @param Repository $repository
     */
    public function __construct(Presenter $presenter, VariablesAdapter $adapter, Repository $repository)
    {
        $this->presenter        = $presenter;
        $this->variablesAdapter = $adapter;
        $this->repository       = $repository;
    }

    /**
     * default index action
     * 
     * @return String $type
     */
    public function index($type = null)
    {
        return $this->presenter->table($type);
    }

    /**
     * shows edit form
     * 
     * @param mixed $id
     * @param String $locale
     * @param IndexListener $listener
     * @return View
     */
    public function edit($id, $locale, IndexListener $listener)
    {
        app('antares.asset')->container('antares/foundation::application')->add('ckeditor', '/packages/ckeditor/ckeditor.js', ['webpack_forms_basic']);
        $model = $this->repository->findByLocale($id, $locale);
        if (is_null($model)) {
            throw new ModelNotFoundException('Model not found');
        }
        return $this->presenter->edit($model, $locale);
    }

    /**
     * updates notification notification
     * 
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function update(IndexListener $listener)
    {
        $id    = Input::get('id');
        $model = $this->repository->find($id);
        $form  = $this->presenter->getForm($model);
        if (!$form->isValid()) {
            return $listener->updateValidationFailed($id, $form->getMessageBag());
        }
        try {
            $this->repository->updateNotification($id, Input::all());
        } catch (Exception $ex) {
            return $listener->updateFailed();
        }

        return $listener->updateSuccess();
    }

    /**
     * sends test notification
     * 
     * @param IndexListener $listener
     * @param mixed $id
     * @return JsonResponse
     */
    public function sendtest(IndexListener $listener, $id = null)
    {
        $notifier = null;
        $type     = null;

        if (app('request')->isMethod('post')) {
            $inputs   = Input::all();
            $content  = $this->variablesAdapter->fill($inputs['content']);
            preg_match_all('/\[\[(.*?)\]\]/', $content, $matches);
            $content  = str_replace($matches[0], $matches[1], $content);
            $title    = $inputs['title'];
            $type     = $inputs['type'];
            $notifier = app(NotificationHandler::class)->getNotifierAdapter($type);
        } else {
            $model    = $this->repository->find($id);
            $notifier = app(NotificationHandler::class)->getNotifierAdapter($model->type->name);
            $content  = $model->contents->first()->content;
            $title    = $model->contents->first()->title;
        }


        try {
            if (is_null($notifier)) {
                throw new Exception('Unable to resolve notifier adapter.');
            }
            $notifier->send($content, [], function($m) use($title, $type) {
                $to = $type == 'sms' ? config('antares/notifications::default.sms') : user()->email;
                $m->to($to);
                $m->subject($title);
            });
            $result = $notifier->getResultCode();
            if (!$result) {
                throw new Exception($notifier->getResultMessage());
            }
            if (app('request')->ajax()) {
                return new JsonResponse(trans('Message has been sent'), 200);
            }
            return $listener->sendSuccess();
        } catch (Exception $ex) {
            if (app('request')->ajax()) {
                return new JsonResponse($ex->getMessage(), 500);
            }
            return $listener->sendFailed();
        }
    }

    /**
     * preview notification notification
     * 
     * @return View
     */
    public function preview()
    {
        $inputs  = Input::all();
        $content = str_replace("&#39;", '"', $inputs['content']);
        $content = $this->variablesAdapter->get($content);

        preg_match_all('/\[\[(.*?)\]\]/', $content, $matches);
        $view = str_replace($matches[0], $matches[1], $content);

        if (array_get($inputs, 'type') == 'email') {
            event('antares.notifier.before_send_email', [&$view]);
        }


        $brandTemplate = \Antares\Brands\Model\BrandOptions::query()->where('brand_id', brand_id())->first();
        $header        = str_replace('</head>', '<style>' . $brandTemplate->styles . '</style></head>', $brandTemplate->header);
        $html          = preg_replace("/<body[^>]*>(.*?)<\/body>/is", '<body>' . $view . '</body>', $header . $brandTemplate->footer);

        array_set($inputs, 'content', $html);
        return $this->presenter->preview($inputs);
    }

    /**
     * change notification notification status
     * 
     * @param IndexListener $listener
     * @param mixed $id
     * @return RedirectResponse
     */
    public function changeStatus(IndexListener $listener, $id)
    {
        $model = $this->repository->find($id);
        if (is_null($model)) {
            return $listener->changeStatusFailed();
        }
        $model->active = ($model->active) ? 0 : 1;
        $model->save();
        return $listener->changeStatusSuccess();
    }

    /**
     * Create notification notification form
     * 
     * @param String $type
     * @return View
     */
    public function create($type = null)
    {
        app('antares.asset')->container('antares/foundation::application')->add('ckeditor', '/packages/ckeditor/ckeditor.js', ['webpack_forms_basic']);
        return $this->presenter->create($this->repository->makeModel()->getModel(), $type);
    }

    /**
     * store new notification notification
     * 
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function store(IndexListener $listener)
    {
        $model = $this->repository->makeModel()->getModel();
        $form  = $this->presenter->getForm($model)->onCreate();
        if (!$form->isValid()) {
            return $listener->storeValidationFailed($form->getMessageBag());
        }
        try {
            $this->repository->store(Input::all());
        } catch (Exception $ex) {
            return $listener->createFailed();
        }
        return $listener->createSuccess();
    }

    /**
     * deletes custom notification
     * 
     * @param mixed $id
     * @param IndexListener $listener
     * @return RedirectResponse
     */
    public function delete($id, IndexListener $listener)
    {
        try {
            $model = $this->repository->makeModel()->findOrFail($id);
            $model->delete();
            return $listener->deleteSuccess();
        } catch (Exception $ex) {
            return $listener->deleteFailed();
        }
    }

}
