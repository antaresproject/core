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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



return [
    'di'         => [
        'Antares\Logger\Contracts\IndexPresenter'     => 'Antares\Logger\Http\Presenters\IndexPresenter',
        'Antares\Logger\Contracts\ActivityPresenter'  => 'Antares\Logger\Http\Presenters\ActivityPresenter',
        'Antares\Logger\Contracts\ModulesPresenter'   => 'Antares\Logger\Http\Presenters\ModulesPresenter',
        'Antares\Logger\Contracts\SystemPresenter'    => 'Antares\Logger\Http\Presenters\SystemPresenter',
        'Antares\Logger\Contracts\RequestPresenter'   => 'Antares\Logger\Http\Presenters\RequestPresenter',
        'Antares\Logger\Contracts\ReportPresenter'    => 'Antares\Logger\Http\Presenters\ReportPresenter',
        'Antares\Logger\Contracts\DownloadPresenter'  => 'Antares\Logger\Http\Presenters\DownloadPresenter',
        'Antares\Logger\Contracts\HistoryPresenter'   => 'Antares\Logger\Http\Presenters\HistoryPresenter',
        'Antares\Logger\Contracts\AnalyzePresenter'   => 'Antares\Logger\Http\Presenters\AnalyzePresenter',
        'Antares\Logger\Contracts\GeneratorPresenter' => 'Antares\Logger\Http\Presenters\GeneratorPresenter',
        'Antares\Logger\Contracts\FactoryInterface'   => 'Antares\Logger\LoggerFactory',
    ],
    'scripts'    => [
        'resources' => [
            'logger-js' => 'js/logger.js',
        ],
        'reports'   => [
            'theme-default-css' => 'css/theme_default.css',
            'logger-icons-css'  => 'css/icons.css',
            'logger-mobile-css' => 'css/mobile.css',
            'logger-scripts'    => 'css/scripts.min.js',
            'logger-js'         => 'js/logger.js'
        ]
    ],
    'memory'     => [
        'model' => '\Antares\Logger\Model\Checksum'
    ],
    'adapter'    => [
        'default' => [
            'model' => 'Antares\Logger\Adapter\CurlAdapter',
            'url'   => 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : url()->to('')) . '/admin/tickets/exception',
        ],
    ],
    'analyzer'   => [
        'actions' => [
            'server'     => 'Server Environment',
            'system'     => 'System Environment',
            'modules'    => 'System and Modules Files',
            'version'    => 'System Version',
            'database'   => 'Database configuration',
            'logs'       => 'Logs Summary',
            'components' => 'Components and Modules List',
            'checksum'   => 'System Changes',
        ],
        'system'  => require __DIR__ . '/system.php',
        'scripts' => [
            'resources' => [
                'theme-default-css' => 'css/theme_default.css',
                'logger-icons-css'  => 'css/icons.css',
                'logger-scripts'    => 'css/scripts.min.js',
                'logger-js'         => 'js/logger.js',
            ]
        ],
    ],
    /* ------------------------------------------------------------------------------------------------
      |  Menu settings
      | ------------------------------------------------------------------------------------------------
     */
    'menu'       => [
        'filter-route'  => 'logger::logs.filter',
        'icons-enabled' => true,
    ],
    /**
     * collection of predefined activity key names
     */
    'operations' => [
        'login' => 'USERAUTHLISTENER_ONUSERLOGIN'
    ],
];
