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


namespace Antares\Users\Memory;

use Antares\Model\UserMeta;

class Avatar
{

    /**
     * User avatar
     *
     * @var String
     */
    protected $avatar;

    /**
     * Constructing
     */
    public function __construct()
    {
        $this->avatar = UserMeta::where(['user_id' => auth()->user()->id, 'name' => 'picture'])->first();
    }

    /**
     * Gets user avatar
     * 
     * @return mixed
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

}
