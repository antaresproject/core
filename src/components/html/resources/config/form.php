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
return [
    /**
     * cachable memory configuration
     */
    'memory'     => [
        'default'     => [
            'model' => 'Antares\Html\Model\Form',
            'cache' => false,
            'crypt' => false
        ],
        'form-config' => [
            'model' => 'Antares\Html\Model\FormConfig',
            'cache' => false,
            'crypt' => false
        ],
    ],
    'format'     => '<p class="help-block error">:message</p>',
    'submit'     => 'antares/foundation::label.submit',
    /** default form view * */
    'view'       => 'antares/foundation::layouts.antares.partials.form.horizontal',
    /** when form does not have any of control * */
    'empty_form' => 'antares/foundation::layouts.antares.partials.form.empty',
    'dropzone'   => [
        'dropzone-js' => '/js/dropzone.js',
    ],
    'templates'  => [
        'input'         => ['class' => 'form-control'],
        'checkbox'      => ['class' => '', 'data-icheck' => 'true'],
        'switch_field'  => ['class' => 'switch-checkbox', 'aria-required' => "true"],
        'radio'         => ['class' => '', 'data-icheck' => 'true'],
        'password'      => ['class' => 'form-control'],
        'select'        => ['class' => '', 'data-selectar' => true],
        'textarea'      => ['class' => 'form-control'],
        'dropzone'      => ['class' => ''],
        'ckeditor'      => ['class' => ''],
        'remote_select' => ['class' => ''],
        'button'        => ['class' => 'btn btn--md btn--primary mdl-button mdl-js-button mdl-js-ripple-effect'],
        'custom'        => ['class' => ''],
    ],
    'ckeditor'   => [
        'scripts' => [
            'ckeditor-js' => 'packages/ckeditor/ckeditor.js'
        ]
    ],
    'presenter'  => 'Antares\Html\Form\BootstrapThreePresenter',
    'validator'  => [
        'ajaxable' => [
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'errorCss'         => 'has-error',
            'errorCssClass'    => 'has-error',
            'summaryID'        => 'summary-form_es_'
        ]
    ],
    'scripts'    => [
        'client-side' => [
            'resources' => [
                'bootstrap-validator' => 'packages/core/js/validator.min.js'
            ],
            'position'  => 'antares/foundation::scripts'
        ],
        'ajax-side'   => [
            'resources' => [
                'active-form'    => 'packages/core/js/yii_active_forms.js',
                'validation-css' => 'packages/core/css/validation.css',
            ],
            'position'  => 'antares/foundation::scripts'
        ]
    ]
];
