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

namespace Antares\Twig\Extension;

use Thomaswelton\LaravelGravatar\Facades\Gravatar;
use Antares\Users\Memory\Avatar as AvatarMemory;
use Laravolt\Avatar\Facade as AvatarFacade;
use Illuminate\Filesystem\Filesystem;
use Intervention\Image\Facades\Image;
use Antares\Brands\Model\Brands;
use Intervention\Image\Response;
use Twig_SimpleFunction;
use Antares\Model\User;
use Twig_Extension;

/**
 * Access Laravels asset class in your Twig templates.
 */
class Avatar extends Twig_Extension
{
    /*     * 64
     * 
     * Filesystem instance
     *
     * @var Filesystem
     */

    protected $filesystem;

    /**
     * constructing
     * 
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_Avatar';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        $avatar = new Twig_SimpleFunction('avatar', function ($name, $type, $width = 40, $height = 40) {
            return $this->show($name, $type, $width, $height);
        });

        $avatarWidget = new Twig_SimpleFunction('avatar_component', function ($name, $width = 116, $height = 82) {
            return $this->show($name, null, $width, $height, true);
        });

        $gravatar = new Twig_SimpleFunction('gravatar', function ($email, $width = 40, $height = 40) {
            return Gravatar::src($email, $width);
        });
        $profilePicture = new Twig_SimpleFunction('profile_picture', function ($email, $width = 40, $height = 40) {
            if ($email === 'default') {
                return config('antares/users.default_profile_picture_path');
            }
            try {
                $meta = app(AvatarMemory::class)->getAvatar();
            } catch (\Exception $ex) {
                return Gravatar::src($email, $width);
            }
            if (is_null($meta)) {
                return Gravatar::src($email, $width);
            }
            return $meta->value;
        });

        return [
            $avatar, $avatarWidget, $gravatar, $profilePicture
        ];
    }

    /**
     * shows avatar
     * 
     * @param mixed $param
     * @param String $type
     * @param mixed $width
     * @param mixed $height
     * @param boolean $widget
     * @return Response
     */
    protected function show($param, $type = null, $width = 40, $height = 40, $widget = false)
    {
        $filename = public_path('avatars/' . implode('_', [$type, (is_numeric($param) ? $param : camel_case($param)), $width, $height]) . '.png');
        if (!$this->filesystem->exists(dirname($filename))) {
            $this->filesystem->makeDirectory(dirname($filename));
        }
        if ($this->filesystem->exists($filename)) {
            return $this->response($filename);
        }
        if ($widget) {
            $this->createWidgetInitials($filename, $param, $width, $height);
            return $this->response($filename);
        }

        if (!is_numeric($param)) {
            $this->createInitials($filename, $param, $width, $height);
            return $this->response($filename);
        }
        switch ($type) {
            case 'brand':
                $name = app(Brands::class)->where('id', $param)->first()->name;
                break;
            default:
                $user = auth()->user();
                $name = ($param == $user->id) ? $user->fullname : app(User::class)->where('id', $param)->first()->fullname;
                break;
        }
        $this->createInitials($filename, $name, $width, $height);
        return $this->response($filename);
    }

    /**
     * create initials from string
     * 
     * @param String $filename
     * @param String $fullname
     * @param mixed $width
     * @param mixed $height
     * @return Avatar
     */
    protected function createInitials($filename, $fullname, $width = null, $height = null)
    {
        $width        = is_null($width) ? config('avatar.width') : $width;
        $height       = is_null($height) ? config('avatar.height') : $height;
        $defaultWidth = config('avatar.width');
        $fontSize     = config('avatar.fontSize');
        $fontSize     = (((int) $width - $defaultWidth) >= 10) ? $fontSize     += ($width - $defaultWidth) / 2 : $fontSize;
        $avatar       = AvatarFacade::create($fullname)->setDimension($width, $height)->setFontSize($fontSize)->setShape('circle')->setBorder(1, '#3bc975');

        $avatar->save($filename, 100);
        return $avatar;
    }

    /**
     * create widget initials from string
     * 
     * @param String $filename
     * @param String $title
     * @param mixed $width
     * @param mixed $height
     * @return Avatar
     */
    protected function createWidgetInitials($filename, $title, $width = null, $height = null)
    {

        $avatar = AvatarFacade::create($title)->setDimension($width, $height)->setBackground('#02a8f3')->setFontSize(40)->setShape('square');
        $avatar->save($filename, 100);
        return $avatar;
    }

    /**
     * create image response 
     * 
     * @param String $filename
     * @return Response
     */
    protected function response($filename)
    {
        Image::make($filename)->response('png', 100);
        return str_replace([public_path(), DIRECTORY_SEPARATOR], ['', '/'], $filename);
    }

}
