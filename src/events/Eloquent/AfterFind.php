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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Eloquent;

use Antares\Html\Form\FormBuilder;
use Antares\Foundation\Events\AbstractEvent;
use Illuminate\Database\Eloquent\Model;

class AfterFind extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Eloquent: after find statement';

    /** @var string */
    protected static $description = 'Runs after finding model from database';

    /** @var Model */
    public $model;

    /**
     * AfterFind constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        parent::__construct();
    }

}
