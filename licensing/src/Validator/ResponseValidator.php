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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Licensing\Validator;

use Illuminate\Foundation\Application;

class ResponseValidator
{

    /**
     * \Antares\Memory\Provider instance
     *
     * @var \Antares\Memory\Provider 
     */
    protected $memory;

    /**
     * how often license should be verified (in hours)
     *
     * @var mixed 
     */
    protected $interval;

    /**
     * alert adapter instance
     *
     * @var mixed 
     */
    protected $adapter;

    /**
     * constructor
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->memory   = $app->make('antares.memory')->make('primary');
        $this->interval = config('antares/licensing::validation.interval');
        $this->adapter  = $app->make(config('antares/licensing::validation.alert.adapter'));
    }

    /**
     * process license validation
     * 
     * @return boolean
     */
    public function validate()
    {
        if (!$this->shouldBeValidated()) {
            return;
        }
        $validation = app('antares.license')->validate();
        if ($validation['RESULT'] !== 'OK') {
            $this->adapter->run($validation);
        } else {
            $this->memory->put('license', date('Y-m-d H:i:s'));
            $this->memory->finish();
        }

        return true;
    }

    /**
     * checks whether license should be validated
     * 
     * @return boolean
     */
    protected function shouldBeValidated()
    {
        $license = $this->memory->get('license');
        return is_null($license) or ( (time() - strtotime($license)) / 3600) > $this->interval;
    }

}
