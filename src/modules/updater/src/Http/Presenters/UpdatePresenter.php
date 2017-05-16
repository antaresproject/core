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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Http\Presenters;

use Antares\Updater\Contracts\UpdatePresenter as PresenterContract;
use Illuminate\Contracts\Container\Container;
use Antares\Updater\Contracts\RedAlert;

class UpdatePresenter implements PresenterContract
{

    /**
     * application container
     * 
     * @var Container
     */
    protected $container;

    /**
     * alert instance
     *
     * @var RedAlert
     */
    protected $alert;

    /**
     * constructing
     * 
     * @param Container $container
     */
    public function __construct(Container $container, RedAlert $alert)
    {
        $this->container = $container;
        $this->alert     = $alert;
    }

    /**
     * result when update package completed successfully
     * 
     * @param String $version
     * @param boolean $hasError
     * @param array $data
     * @return array
     */
    public function success($version, $hasError = false, array $data = array())
    {
        $this->alert($hasError, ['version' => $version]);
        return $data;
    }

    /**
     * result when update package is invalid
     * 
     * @param array $messages
     * @return type
     */
    public function failed(array $messages = array())
    {
        return implode("<br/>", array_values($messages));
    }

    /**
     * create alert
     * 
     * @param boolean $hasError
     * @param array $successAttributes
     */
    protected function alert($hasError = false, array $successAttributes = array())
    {
        $alertAttributes = [
            'showCancelButton'  => true,
            'showConfirmButton' => false,
            'cancelButtonText'  => "Close",
            'closeOnCancel'     => true
        ];
        if ($hasError) {
            $alertAttributes = array_merge($alertAttributes, ['title' => 'Error appears while updating', 'type' => "error"]);
            $view            = view('antares/updater::admin.partials._error');
        } else {
            $alertAttributes = array_merge($alertAttributes, ['title' => 'System has been updated successfully', 'type' => "success"]);
            $view            = view('antares/updater::admin.partials._success', $successAttributes);
        }
        $this->alert->build($view, $alertAttributes);
    }

}
