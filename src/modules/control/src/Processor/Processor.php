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



namespace Antares\Control\Processor;

abstract class Processor
{

    /**
     * The foundation implementation.
     *
     * @var \Antares\Contracts\Foundation\Foundation
     */
    protected $foundation;

    /**
     * Memory instance.
     *
     * @var \Antares\Contracts\Memory\Provider
     */
    protected $memory;

    /**
     * Model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Presenter instance.
     *
     * @var object
     */
    protected $presenter;

    /**
     * Validation instance.
     *
     * @var object
     */
    protected $validator;

}
