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


namespace Antares\Twig\Extension\Laravel;

use Antares\Html\Support\FormBuilder;
use TwigBridge\Extension\Laravel\Form as SupportExtension;

/**
 * Access Laravels form builder in your Twig templates.
 */
class Form extends SupportExtension
{

    /**
     * @var \Collective\Html\FormBuilder
     */
    protected $form;

    /**
     * Create a new form extension
     *
     * @param \Collective\Html\FormBuilder
     */
    public function __construct(FormBuilder $form)
    {
        $this->form = $form;
    }

}
