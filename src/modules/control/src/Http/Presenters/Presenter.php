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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Control\Http\Presenters;

use Antares\Contracts\Html\Form\Grid;
use Antares\Foundation\Http\Presenters\Presenter as SupportPresenter;

class Presenter extends SupportPresenter
{

    /**
     * Implementation of form contract.
     *
     * @var \Antares\Contracts\Html\Form\Factory
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function handles($url)
    {
        return handles($url);
    }

    /**
     * {@inheritdoc}
     */
    public function setupForm(Grid $form)
    {
        $form->layout('antares/foundation::components.form');
    }

}
