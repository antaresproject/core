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


namespace Antares\Auth\Composers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Crypt;
use Urlcrypt\Urlcrypt;

class MultiuserPlaceholder
{

    /**
     * @var Application 
     */
    protected $app;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * up component placeholders
     */
    public function onBootExtension()
    {
        $auth = auth();
        if ($auth->guest()) {
            return;
        }
        $token = $auth->guard('web')->getSession()->get('auth');
        if (is_null($token)) {
            return;
        }

        $data = @unserialize(Crypt::decrypt($token));

        if (!$this->validate($data)) {
            return;
        }
        $key = Urlcrypt::encode(implode(':', [array_get($data, 'primary_user_id'), array_get($data, 'from'), rand(0, 99999), env('APP_KEY')]));
        $url = "/antares/logout/with/" . $key;
        $this->app->make('antares.widget')
                ->make('placeholder.multiuser')
                ->add('multiuser')
                ->value(view('antares/foundation::users.partials._multiuser', compact('url')));
    }

    /**
     * whether user is in preview state
     * 
     * @param array $data
     * @return boolean
     */
    protected function validate(array $data = array())
    {
        $id = auth()->user()->id;
        if (empty($data)) {
            return false;
        }
        if ((int) array_get($data, 'secondary_user_id') !== $id) {
            return false;
        }

        $primary = array_get($data, 'primary_user_id');
        if ($primary === null) {
            return false;
        }
        return true;
    }

}
