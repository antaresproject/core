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

namespace Antares\Notifications\Http\Presenters;

use Antares\Notifications\Contracts\IndexPresenter as PresenterContract;
use Antares\Notifications\Http\Datatables\Notifications as Datatables;
use Antares\View\Notification\Notification;
use Antares\Notifications\Http\Form\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Antares\Html\Form\FormBuilder;
use Illuminate\Support\Fluent;
use Illuminate\View\View;
use Exception;

class IndexPresenter implements PresenterContract
{

    /**
     * breadcrumbs instance
     *
     * @var Breadcrumb
     */
    protected $breadcrumb;

    /**
     * Datatables instance
     *
     * @var Datatables
     */
    protected $datatables;

    /**
     * constructing
     * 
     * @param Breadcrumb $breadcrumb
     * @param Datatables $datatables
     */
    public function __construct(Breadcrumb $breadcrumb, Datatables $datatables)
    {
        $this->breadcrumb = $breadcrumb;
        $this->datatables = $datatables;
    }

    /**
     * Table View Generator
     * 
     * @param String $type
     * @return View
     */
    public function table($type = null)
    {
        $this->breadcrumb->onTable($type);
        return $this->datatables->render('antares/notifications::admin.index.index');
    }

    /**
     * shows form edit job
     * 
     * @param Model $eloquent
     * @param String $locale
     * @return View
     */
    public function edit($eloquent, $locale)
    {
        $this->breadcrumb->onEdit($eloquent);
        $form = $this->getForm($eloquent, $locale);
        return $this->view('edit', compact('form'));
    }

    /**
     * gets form instance
     * 
     * @param Model $eloquent
     * @return FormBuilder
     * @throws Exception
     */
    public function getForm($eloquent, $type = null)
    {
        $classname     = $eloquent->classname;
        $model         = $eloquent->contents->first();
        $configuration = [
            'content'   => $model !== null ? $model->content : null,
            'title'     => !is_null($model) ? $model->title : '',
            'type'      => ($eloquent->exists ? $eloquent->type->name : ''),
            'form_name' => $eloquent->name
        ];
        $notification  = app(!strlen($classname) ? Notification::class : $classname);
        $fluent        = new Fluent(array_merge($configuration, array_except($eloquent->toArray(), ['type'])));
        if (!is_null($fluent->type)) {
            $fluent->type = $type;
        }
        return $this->form($fluent, $notification);
    }

    /**
     * gets instance of command form
     * 
     * @param Fluent $fluent
     * @param mixed $notification
     * @return FormBuilder
     */
    protected function form(Fluent $fluent, $notification = null)
    {
        publish('notifications', 'scripts.resources-default');
        Event::fire('antares.forms', 'notification.' . $fluent->form_name);
        $fluent->type = $fluent->type == '' ? 'email' : $fluent->type;
        return new Form($notification, $fluent);
    }

    /**
     * preview notification notification
     * 
     * @param array $data
     * @return View
     */
    public function preview(array $data)
    {
        return $this->view('preview', $data);
    }

    /**
     * create new notification notification
     * 
     * @param Model $model
     * @param String $type
     * @return View
     */
    public function create(Model $model, $type = null)
    {
        $this->breadcrumb->onCreate($type);
        $form = $this->getForm($model, $type)->onCreate();

        return $this->view('create', ['form' => $form]);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     *
     * @return View
     */
    public function view($view, $data = [], $mergeData = [])
    {
        return view('antares/notifications::admin.index.' . $view, $data, $mergeData);
    }

}
