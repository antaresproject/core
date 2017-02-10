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
 * @package    Antares Core
 * @version    0.9.0
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Extension;

use Illuminate\Http\Request;
use Illuminate\Contracts\Config\Repository;
use Antares\Contracts\Extension\StatusChecker as StatusCheckerContract;

class StatusChecker implements StatusCheckerContract
{

    /**
     * Config instance.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Mode status.
     *
     * @var string|null
     */
    protected $status;

    /**
     * Construct a new Application instance.
     *
     * @param  \Illuminate\Contracts\Config\Repository  $config
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Repository $config, Request $request)
    {
        $this->config  = $config;
        $this->request = $request;
    }

    /**
     * Check current mode is equal given $mode.
     *
     * @param  string  $mode
     *
     * @return bool
     */
    public function is($mode)
    {
        return ($this->mode() === $mode);
    }

    /**
     * Check current mode is not equal given $mode.
     *
     * @param  string  $mode
     *
     * @return bool
     */
    public function isNot($mode)
    {
        return ($this->mode() !== $mode);
    }

    /**
     * Get current mode.
     *
     * @return string
     */
    public function mode()
    {
        if (is_null($this->status)) {
            $this->verifyStatus();
        }

        return $this->status;
    }

    /**
     * Verify safe mode status.
     *
     * @return void
     */
    protected function verifyStatus()
    {
        $config = $this->config->get('antares/extension::mode', 'normal');
        $input  = $this->request->input('_mode', $config);

        if ($input == 'safe') {
            $this->enableSafeMode();
        } else {
            $this->disableSafeMode();
        }
    }

    /**
     * Disable safe mode.
     *
     * @return void
     */
    protected function disableSafeMode()
    {
        $this->config->set('antares/extension::mode', $this->status = 'normal');
    }

    /**
     * Enable safe mode.
     *
     * @return void
     */
    protected function enableSafeMode()
    {
        $this->config->set('antares/extension::mode', $this->status = 'safe');
    }

}
