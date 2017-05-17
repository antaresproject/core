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


namespace Antares\Users\Http\Presenters;

use Antares\Users\Http\Form\User as UserForm;
use Antares\Users\Http\Breadcrumb\Breadcrumb;
use Antares\Users\Http\Datatables\Users;
use Antares\Contracts\Html\Form\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Presenter
{

    /**
     * datatables instance
     *
     * @var \Antares\Users\Http\Datatables\Users
     */
    protected $datatables;

    /**
     * breadcrumb instance
     *
     * @var Breadcrumb 
     */
    protected $breadcrumb;

    /**
     * Construct a new User presenter.
     * 
     * @param \Antares\Users\Http\Datatables\Users $datatables
     * @param Breadcrumb $breadcrumb
     */
    public function __construct(Users $datatables, Breadcrumb $breadcrumb)
    {
        $this->datatables = $datatables;
        $this->breadcrumb = $breadcrumb;
    }

    /**
     * Table View Generator for Antares\Model\Brands.
     * @return \Illuminate\View\View
     */
    public function table()
    {
        return $this->datatables->render('antares/foundation::users.index');
    }

    /**
     * shows user details
     * 
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function show(Model $model)
    {
        $this->breadcrumb->onShowUser($model);
        return view('antares/foundation::users.show', ['model' => $model]);
    }

    /**
     * Form View Generator for Antares\Model\User.
     *
     * @param  User  $model
     *
     * @return Builder
     */
    public function form($model)
    {
        $this->breadcrumb->onCreateOrEdit($model);
        return new UserForm($model);
    }

}
