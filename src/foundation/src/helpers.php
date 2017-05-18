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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Debug\Dumper;
use Antares\Messages\SwalMessanger;
use Antares\Html\Form\Field;

if (!function_exists('users')) {

    /**
     * Gets list of user by role names
     */
    function users($roles = null)
    {
        $builder = app('antares.user')->newQuery();
        if (is_null($roles)) {
            return $builder->get();
        }
        return $builder->role((is_string($roles) ? (array) $roles : $roles))->get();
    }

}

if (!function_exists('defaults')) {

    /**
     * Gets default value when attribute is not set
     */
    function defaults($object = null, $key = null, $default = '---')
    {
        if (is_null($object) or empty($object)) {
            return $default;
        }
        if (is_array($object)) {
            return array_get($object, $key, $default);
        }

        return (!isset($object->{$key}) or is_null($object->{$key})) ? $default : $object->{$key};
    }

}
if (!function_exists('ajax')) {

    /**
     * Whether request is ajax request
     */
    function ajax($key = null)
    {
        if (!request()->ajax()) {
            return false;
        }
        return is_null($key) ? true : input($key);
    }

}

if (!function_exists('component_color')) {

    /**
     * Component color getter
     */
    function component_color($name)
    {
        return array_get(components_colors(), $name, '#000');
    }

}


if (!function_exists('is_api_request')) {

    /**
     * Whether request is api request
     */
    function is_api_request()
    {
        $request = request();
        return $request->wantsJson() && str_contains($request->header('Accept'), implode('.', [env('API_STANDARDS_TREE'), env('API_SUBTYPE'), env('API_VERSION')]));
    }

}


if (!function_exists('bound')) {

    /**
     * Verify whether alias name is available in app
     */
    function bound($name)
    {
        return app()->bound($name);
    }

}


if (!function_exists('user_status')) {

    /**
     * Shows name of user status
     */
    function user_status($user, $html = true)
    {
        $user = (array) $user;
        $name = ((int) $user['status']) ? 'ACTIVE' : 'Archived';
        if ($html) {
            return ((int) $user['status']) ? '<span class="label-basic label-basic--success">' . $name . '</span>' : '<span class="label-basic label-basic--danger">' . $name . '</span>';
        }
        return $name;
    }

}

if (!function_exists('listen')) {

    /**
     * Event listener
     */
    function listen($event, $params = null)
    {
        return app('events')->listen($event, $params);
    }

}

if (!function_exists('notify')) {

    /**
     * Creates system notification
     */
    function notify($event, array $params = [])
    {
        return event($event, $params);
    }

}

if (!function_exists('sms_notification')) {

    /**
     * Adds sms notification to send queue
     */
    function sms_notification($event, array $recipients = [], array $variables = [])
    {
        return event($event, [$variables, $recipients]);
    }

}

if (!function_exists('email_notification')) {

    /**
     * Adds email notification to send queue
     */
    function email_notification($event, array $recipients = [], array $variables = [])
    {
        return event($event, [$variables, $recipients]);
    }

}

if (!function_exists('locale')) {

    /**
     * Gets current locale
     */
    function locale()
    {
        return app()->getLocale();
    }

}

if (!function_exists('inputs')) {

    /**
     * Gets all data from input
     */
    function inputs()
    {
        return Input::all();
    }

}
if (!function_exists('input')) {

    /**
     * Gets data from input
     */
    function input($key, $default = null)
    {
        return array_get(inputs(), $key, $default);
    }

}

if (!function_exists('app_installed')) {

    /**
     * Checks whether application is installed
     */
    function app_installed()
    {
        return (int) app('antares.memory')->get('app.installed');
    }

}

if (!function_exists('area')) {

    /**
     * Gets current user area
     */
    function area()
    {
        $segment = request()->segment(1);
        return !is_null($segment) ? $segment : config('areas.default');
    }

}


if (!function_exists('user')) {

    /**
     * User details getter
     */
    function user($id = null)
    {
        return !is_null($id) ? app('antares.user')->newQuery()->whereId($id)->first() : auth()->user();
    }

}

if (!function_exists('swal')) {

    /**
     * Shows swal dialog box message
     */
    function swal($type, $title, $text = null)
    {
        app(SwalMessanger::class)->setType($type)->message($title, $text);
    }

}

if (!function_exists('priority_label')) {

    /**
     * Creates colorized priority label
     */
    function priority_label($name)
    {
        $class = 'pending';
        switch ($name) {
            case 'low':
                $class = 'success';
                break;
            case 'highest':
                $class = 'danger';
                break;
            case 'high':
                $class = 'danger';
                break;
        }
        return '<span class="label-basic label-basic--' . $class . '">' . $name . '</span>';
    }

}


if (!function_exists('sandbox_path')) {

    /**
     * Gets sandbox public path
     */
    function sandbox_path($path = null)
    {
        $sandboxMode = app('request')->get('sandbox');
        if ($sandboxMode) {
            $version = 'build_' . str_replace(['.', ','], '_', $sandboxMode);
            return getcwd() . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . $path;
        }
        return public_path($path);
    }

}

if (!function_exists('wrap_html')) {

    /**
     * Wraps html string into rows
     */
    function wrap_html($str, $size = 50, $splitter = '<br />')
    {
        $html   = false;
        $i      = 0;
        $chars  = str_split($str);
        $return = "";
        $break  = false;
        foreach ($chars as $char) {
            if ($char == "<") {
                $html = true;
            }
            if ($char == ">") {
                $html = false;
            }
            if (!$html) {
                $i++;
            }
            $return .= $char;
            if ($i == $size) {
                $break = true;
            }
            if ($char == " " && !$html && $break) {
                $return .= $splitter;
                $i      = 0;
                $break  = false;
            }
        }
        return $return;
    }

}


if (!function_exists('lang_id')) {

    /**
     * Get current language id
     */
    function lang_id($locale = null)
    {
        return lang($locale)->id;
    }

}
if (!function_exists('lang')) {

    /**
     * Get current language id
     */
    function lang($locale = null)
    {
        $code = !is_null($locale) ? $locale : app()->getLocale();
        return \Antares\Translations\Models\Languages::where('code', $code)->first();
    }

}
if (!function_exists('langs')) {

    /**
     * Get languages list
     */
    function langs()
    {
        return \Antares\Translations\Models\Languages::all();
    }

}

if (!function_exists('components_colors')) {

    /**
     * Get components colors map
     */
    function components_colors()
    {
        $extensions = array_keys(app('antares.memory')->make('component')->get('extensions.active'));
        $colors     = config('colors');
        $components = array_map(function($current) {
            return trim(str_replace('components', '', $current), '/');
        }, $extensions);
        $return = [];
        foreach ($components as $index => $component) {
            $color = array_get($colors, $index + 1, '000');
            array_set($return, $component, '#' . $color);
        }
        return $return + ['core' => '#' . $colors[0]];
    }

}

if (!function_exists('user_meta')) {

    /**
     * Get data from user meta
     */
    function user_meta($name, $default = null)
    {
        if (auth()->guest()) {
            return $default;
        }
        $metas = user()->meta->filter(function($item) use($name) {
            return $item->name == $name;
        });
        if ($metas->count()) {
            return $metas->first()->value;
        }
        return $default;
    }

}
if (!function_exists('tooltip')) {

    /**
     * tooltip creator
     */
    function tooltip($param = null, $value = null)
    {
        if ($param instanceof Field) {
            $value = $param->tip;
        }
        if (strlen($value) <= 0) {
            return '';
        }
        return '&nbsp;<i class="zmdi zmdi-hc-lg zmdi-info-outline " data-tooltip-inline="' . $value . '"></i>';
    }

}
if (!function_exists('extension_path')) {

    /**
     * extension path getter
     */
    function extension_path($path)
    {
        $name = $path;
        if (count($dirs = explode('/', $path)) > 1) {
            $name = current($dirs);
        }
        unset($dirs[0]);
        $extensions = app('antares.memory')->make('component')->get('extensions.available');
        $extension  = array_where($extensions, function($current) use($name) {
            return str_contains($current, $name) !== false;
        });
        if (empty($extension)) {
            return false;
        }
        $finder = app('antares.extension.finder');
        return $finder->resolveExtensionPath(current($extension)['source-path']) . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $dirs);
    }

}

if (!function_exists('can')) {

    /**
     * checks whether user is allowed to action
     */
    function can($name)
    {
        list($component, $resource) = explode('.', $name);
        $acl = ($component === 'antares') ? app('antares.acl')->make('antares') : app('antares.acl')->make($component);
        return $acl->can($resource);
    }

}

if (!function_exists('extension_active')) {

    /**
     * checks whether extension is active
     */
    function extension_active($name)
    {
        try {
            return app('antares.extension')->isActive($name);
        } catch (\Exception $ex) {
            return false;
        }
    }

}

if (!function_exists('extensions')) {

    /**
     * List of extensions getter
     */
    function extensions($name = null)
    {
        $extensions = app('antares.memory')->make('component')->get('extensions.active');
        $return     = [];
        if (empty($extensions)) {
            return $return;
        }
        if (!is_null($name)) {
            return array_get($extensions, 'antaresproject/component-' . $name);
        }
        foreach ($extensions as $name => $extension) {

            $return[str_replace('components', 'antares', $name)] = $extension;
        }

        return $return;
    }

}

if (!function_exists('from_route')) {

    /**
     * get param from route
     */
    function from_route($key, $default = null)
    {
        if (php_sapi_name() === 'cli') {
            return null;
        }
        $current = Route::current();
        return !is_null($current) ? $current->parameter($key, $default) : $default;
    }

}

if (!function_exists('asset')) {

    /**
     * add asset to asset manager
     */
    function asset($path, $container = 'antares/foundation::scripts')
    {
        $name = snake_case(last(explode('/', $path)));
        return app('antares.asset')->container($container)->add($name, $path);
    }

}

if (!function_exists('memory')) {

    /**
     * get value from main memory
     */
    function memory($key)
    {
        return app('antares.memory')->make('primary')->get($key);
    }

}

if (!function_exists('uri')) {

    /**
     * get uri
     */
    function uri()
    {
        $request = app('request');
        if ($request->ajax()) {
            $referer = $request->server->get('HTTP_REFERER');
            return trim(str_replace(url()->to(''), '', $referer), '/');
        }
        $route = Route::getCurrentRoute();
        return !is_null($route) ? $route->uri() : null;
    }

}
if (!function_exists('brand_id')) {

    /**
     * get current brand id
     */
    function brand_id()
    {
        return (int) app('antares.memory')->make('primary')->get('brand.default');
    }

}
if (!function_exists('brands')) {

    /**
     * Gets list of brands
     */
    function brands()
    {
        return (int) app('antares.memory')->make('primary')->get('brand.default');
    }

}
if (!function_exists('brand_name')) {

    /**
     * Gets current brand name
     */
    function brand_name()
    {
        return app('antares.memory')->make('primary')->get('brand.configuration.name');
    }

}
if (!function_exists('anchor')) {

    function anchor($url, $title, $attributes = [])
    {
        if ($url == '#') {
            return app('html')->link($url, $title, $attributes)->get();
        }
        if (($url = handles_acl($url)) === '#') {
            return '';
        }
        return app('html')->link($url, $title, $attributes)->get();
    }

}


if (!function_exists('format_x_days')) {

    function format_x_days($date, $html = true)
    {
        return app(\Antares\Date\DateFormatter::class)->formatTimeAgoForHumans($date, $html);
    }

}

if (!function_exists('brand_logo')) {

    function brand_logo($param = 'logo', $default = null)
    {
        $registry = app('antares.memory')->make('registry');
        $logoPath = config('antares/brands::logo.default_path');

        try {
            if (in_array($param, ['logo', 'favicon'])) {
                $logo    = $registry->get('brand.configuration.template.favicon');
                $default = asset($logoPath . 'logo_default_tear.png');
                return strlen($logo) <= 0 ? $default : asset('img/logos/' . $logo);
            } elseif ($param == 'big') {
                $logo = $registry->get('brand.configuration.template.logo');
                return !is_null($logo) ? asset('img/logos/' . $logo) : $default;
            }
            return asset('img/logos/' . $param);
        } catch (\Exception $ex) {
            return asset($logoPath . 'logo_default_tear.png');
        }
    }

}

if (!function_exists('publish')) {

    function publish($extension, $options = null, $before = ['require' => ['app_notifications']])
    {
        return app('antares.asset.publisher')->publish($extension, $options, $before);
    }

}

if (!function_exists('vdump')) {

    function vdump()
    {
        array_map(function ($x) {
            (new Dumper)->dump($x);
        }, func_get_args());
    }

}
if (!function_exists('antares')) {

    /**
     * Return antares.app instance.
     *
     * @param  string|null  $service
     *
     * @return mixed
     */
    function antares($service = null)
    {
        if (!is_null($service)) {
            return app("antares.platform.{$service}");
        }

        return app('antares.app');
    }

}

if (!function_exists('memorize')) {

    /**
     * Return memory configuration associated to the request.
     *
     * @param  string   $key
     * @param  string   $default
     *
     * @return mixed
     *
     * @see    \Antares\Foundation\Kernel::memory()
     */
    function memorize($key, $default = null)
    {
        return app('antares.platform.memory')->get($key, $default);
    }

}

if (!function_exists('handles')) {

    /**
     * Return handles configuration for a package/app.
     *
     * @param  string   $name
     * @param  array    $options
     *
     * @return string
     */
    function handles($name, array $options = [])
    {
        return app('antares.app')->handles($name, $options);
    }

}
if (!function_exists('handles_acl')) {

    /**
     * Return handles configuration for a package/app.
     *
     * @param  string   $name
     * @param  array    $options
     *
     * @return string
     */
    function handles_acl($name, array $options = [])
    {
        return app('antares.app')->handles_acl($name, $options);
    }

}

if (!function_exists('resources')) {

    /**
     * Return resources route.
     *
     * @param  string   $name
     * @param  array    $options
     *
     * @return string
     */
    function resources($name, array $options = [])
    {
        $name = ltrim($name, '/');

        return app('antares.app')->handles("antares::resources/{$name}", $options);
    }

}

if (!function_exists('get_meta')) {

    /**
     * Get meta.
     *
     * @param  string   $key
     * @param  mixed    $default
     *
     * @return string
     */
    function get_meta($key, $default = null)
    {
        return app('antares.meta')->get($key, $default);
    }

}

if (!function_exists('set_meta')) {

    /**
     * Set meta.
     *
     * @param  string   $key
     * @param  mixed    $value
     *
     * @return string
     */
    function set_meta($key, $value = null)
    {
        return app('antares.meta')->set($key, $value);
    }

}
if (!function_exists('set_meta')) {

    /**
     * Set meta.
     *
     * @param  string   $key
     * @param  mixed    $value
     *
     * @return string
     */
    function set_meta($key, $value = null)
    {
        return app('antares.meta')->set($key, $value);
    }

}
