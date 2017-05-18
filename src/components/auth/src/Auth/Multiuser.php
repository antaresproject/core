<?php

namespace Antares\Auth;

use Illuminate\Support\Facades\Crypt;
use Urlcrypt\Urlcrypt;

class Multiuser
{

    public function getKey()
    {
        $auth = auth();
        if ($auth->guest()) {
            return false;
        }
        $token = $auth->guard('web')->getSession()->get('auth');
        if (is_null($token)) {
            return false;
        }

        $data = @unserialize(Crypt::decrypt($token));

        if (!$this->validate($data)) {
            return false;
        }
        return Urlcrypt::encode(implode(':', [array_get($data, 'primary_user_id'), array_get($data, 'from'), rand(0, 99999), env('APP_KEY')]));
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
