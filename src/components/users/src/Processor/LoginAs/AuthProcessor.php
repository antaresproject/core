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


namespace Antares\Users\Processor\LoginAs;

use Antares\Users\Http\Controllers\LoginAs\AuthController as Listener;
use Antares\Foundation\Processor\Processor;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Routing\UrlGenerator;
use Antares\Auth\AuthManager;
use Urlcrypt\Urlcrypt;

class AuthProcessor extends Processor
{

    /**
     * auth manager instance
     *
     * @var AuthManager 
     */
    protected $auth;

    /**
     * url generator instance
     *
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * constructing
     * 
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->auth         = auth();
    }

    /**
     * when logging into user area
     * 
     * @param Listener $listener
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Listener $listener, $id)
    {
        $string = [
            'primary_user_id'   => $this->auth->user()->id,
            'secondary_user_id' => $id,
            'from'              => str_replace(url()->to('/'), '', $this->urlGenerator->previous()),
            'time'              => time()
        ];
        $hash   = Crypt::encrypt(serialize($string));

        $this->auth->logout();
        
        
        if (!$this->auth->loginUsingId($id)) {
            return $listener->userLogInFailed();
        }
        $this->auth->guard('web')->getSession()->put('auth', $hash);
        return $listener->userLogInSuccessfully();
    }

    /**
     * logging out from user area
     * 
     * @param Listener $listener
     * @param String $key
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Listener $listener, $key)
    {
        list($uid, $from, $time, $appKey) = $encoded = explode(':', Urlcrypt::decode($key));
        $appKey  = ($appKey === 'base64') ? $appKey . ':' . last($encoded) : $appKey;

        if ($appKey !== env('APP_KEY')) {
            $this->auth->logout();
            return $listener->userLogOutFailed();
        }

        $this->auth->loginUsingId($uid);

        return $listener->userLogOutSuccessfully($from);
    }

}
