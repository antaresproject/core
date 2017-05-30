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

use Illuminate\Support\Fluent as FluentSupport;
use Antares\Asset\JavaScriptDecorator;
use Illuminate\Support\Facades\Event;
use Antares\Acl\MultisessionAcl;
use Antares\UI\WidgetManager;
use Illuminate\View\Factory;
use Illuminate\Support\Str;
use Twig_Function_Method;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Twig_Extension;
use Exception;

/**
 * Access Laravels asset class in your Twig templates.
 */
class Foundation extends Twig_Extension
{

    /**
     * @var string|object
     */
    protected $callbacks = [
        'str' => 'Antares\Support\Str',
        'arr' => 'Illuminate\Support\Arr'
    ];

    /**
     * @var WidgetManager 
     */
    protected $widgetManager;

    /**
     * @var Factory 
     */
    protected $factory;

    /**
     * constructor
     * @param WidgetManager $widgetManager
     * @param Factory $factory
     */
    public function __construct(WidgetManager $widgetManager, Factory $factory)
    {
        $this->widgetManager = $widgetManager;
        $this->factory       = $factory;
    }

    /**
     * Return the string object callback.
     *
     * @return string|object
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Antares_Twig_Extension_Foundation';
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('memorize', function ($key, $default = null) {
                        return app('antares.platform.memory')->get($key, $default);
                    }),
            new Twig_SimpleFunction('decorate', function ($name, $view) {
                        echo app("antares.decorator")->render($name, $view)->render();
                    }),
            new Twig_SimpleFunction('push', function ($__env, $name) {
                        $__env->startSection($name);
                        return;
                    }),
            new Twig_SimpleFunction('endpush', function ($__env) {
                        $__env->appendSection();
                        return;
                    }),
            new Twig_SimpleFunction('view', function ($view = null, $data = [], $mergeData = []) {
                        $factory = app('Illuminate\Contracts\View\Factory');

                        if (func_num_args() === 0) {
                            return $factory;
                        }

                        return $factory->make($view, $data, $mergeData);
                    }),
            new Twig_SimpleFunction('append_panes', function ($panes, $arguments) {
                        $panes->add($arguments['name'], $arguments['position'])
                                ->title($arguments['title'])
                                ->attributes($arguments['attributes'])
                                ->content($arguments['content']);
                        return $panes;
                    }),
            new Twig_SimpleFunction('base_path', function ($path) {
                        return app()->basePath() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
                    }),
            new Twig_SimpleFunction('authFormGroup', function ($authentication, $auth) {
                        $class   = ['form-group'];
                        (false === $authentication) && $class[] = 'error';
                        ('eloquent' !== $auth['driver']) && $class[] = 'hide';
                        return ['class' => implode(' ', $class)];
                    }),
            new Twig_SimpleFunction('unset', function ($data, $keynames) {
                        if (is_array($keynames)) {
                            foreach ($keynames as $keyname) {
                                unset($data[$keyname]);
                            }
                        } elseif (true === isset($data[$keynames])) {
                            unset($data[$keynames]);
                        }
                        return $data;
                    }),
            new Twig_SimpleFunction('handles', function ($slug, $attributes = null) {
                        return handles($slug, $attributes !== null ? $attributes : []);
                    }),
            new Twig_SimpleFunction('e', function ($value = null) {
                        return e($value);
                    }),
            new Twig_SimpleFunction('call_user_func', function ($callable, $row) {
                        return call_user_func($callable, $row);
                    }),
            new Twig_SimpleFunction('fluent', function ($arguments) {
                        isset($arguments['menu']) && $arguments['menu'] = view($arguments['menu']);
                        return new FluentSupport($arguments);
                    }),
            new Twig_SimpleFunction('set_meta', function ($name) {
                        $arguments = func_get_args();
                        app('antares.meta')->set($arguments[0], $arguments[1]);
                        return;
                    }),
            new Twig_SimpleFunction('get_base_meta_title', function ($default = null) {
                        return memory('site.name', $default);
                    }),
            new Twig_SimpleFunction('get_meta', function ($name) {
                        $arguments = func_get_args();
                        $default   = isset($arguments[1]) ? $arguments[1] : null;
                        return app('antares.meta')->get($arguments[0], $default);
                    }),
            new Twig_SimpleFunction('placeholder', function ($name) {
                        $__ps = $this->widgetManager->make("placeholder." . $name);
                        Event::fire("placeholder.before." . $name, [$__ps]);
                        foreach ($__ps as $__p) {
                            echo value($__p->value ?: "");
                        }
                        Event::fire("placeholder.after." . $name, [$__ps]);
                    }),
            new Twig_SimpleFunction('stack', function ($name) {
                        echo $this->factory->yieldContent($name);
                    }),
            new Twig_SimpleFunction('closure', function ($closure) {
                        $arguments = func_get_args();
                        $params    = array_slice($arguments, 1, count($arguments) - 1);
                        return call_user_func_array($closure, $params);
                    }),
            new Twig_SimpleFunction('can', function ($resourceName) {
                        list($component, $resource) = explode('::', $resourceName);
                        return app('antares.acl')->make($component)->can($resource);
                    }),
            new Twig_SimpleFunction('has_error', function ($errors, $control) {
                        if (in_array($control->type, ['select', 'input:checkbox', 'input:radio']) and str_contains($control->name, '[]')) {
                            return $errors->has(str_replace('[]', '', $control->name));
                        }
                        return $errors->has($control->name);
                    }),
            new Twig_SimpleFunction('event', function ($name) {
                        $event     = snake_case(strtolower($name));
                        $arguments = array_slice(func_get_args(), 1);
                        Event::fire($event, $arguments);
                        return '';
                    }),
            new Twig_SimpleFunction('event_gridable', function () {
                        $path = \Illuminate\Support\Facades\Route::getCurrentRoute()->uri();
                        Event::fire('widgets:render.' . $path . '.right');
                        return '';
                    }),
            new Twig_SimpleFunction('event_gridable', function () {
                        $path = \Illuminate\Support\Facades\Route::getCurrentRoute()->uri();
                        Event::fire('widgets:render.' . $path . '.right');
                        return '';
                    }),
            new Twig_SimpleFunction('helper', function ($name, $attributes) {
                        $helper = array_get(config('view-helpers'), $name);
                        if (is_null($helper)) {
                            throw new \Twig_Error_Runtime(sprintf('Helper %s not found.', $name));
                        }
                        if (!isset($helper['classname'])) {
                            throw new \Twig_Error_Runtime(sprintf('Helper %s not found.', $name));
                        }
                        return app()->make($helper['classname'])->setAttributes($attributes)->render();
                    }),
            new Twig_SimpleFunction('control_error', function ($errors, $control) {
                        $error = (in_array($control->type, ['select', 'input:checkbox', 'input:radio']) and str_contains($control->name, '[]')) ?
                                $errors->first(str_replace('[]', '', $control->name)) : $errors->first($control->name);

                        if (!is_null($error)) {
                            return '<p class="help-block error">' . $error . '</p>';
                        }
                        return;
                    }),
            new Twig_SimpleFunction('format_x_days', function ($date, $html = true) {
                        echo format_x_days($date, $html);
                        return;
                    }),
            new Twig_SimpleFunction('hostname', function () {
                        return trim(str_replace(['http', 'https', '://'], '', url('/')), '/');
                    }),
            new Twig_SimpleFunction('user_meta', function ($name, $default = null) {
                        return user_meta($name, $default);
                    }),
            new Twig_SimpleFunction('dd', function () {
                        dd(func_get_args());
                    }),
            new Twig_SimpleFunction('dump', function () {
                        vdump(func_get_args());
                    }),
            new Twig_SimpleFunction('isAjaxRequest', function () {
                        return (int) request()->ajax();
                    }),
            new Twig_SimpleFunction('canOrPrimaryCan', function ($resourceName) {
                        list($component, $resource) = explode('::', $resourceName);

                        return app('antares.acl')->make($component)->can($resource) || app(MultisessionAcl::class)->canPrimary($resource);
                    }),
            new Twig_SimpleFunction('user_status', function ($user, $html = true) {
                        return user_status($user, $html);
                    }),
            new Twig_SimpleFunction('priority_label', function ($name) {
                        return priority_label($name);
                    }),
            new Twig_SimpleFunction('component_color', function ($name) {
                        return component_color($name);
                    }),
            new Twig_SimpleFunction('has_active_in_childs', function ($elements) {
                        foreach ($elements as $element) {
                            if (empty($element->childs)) {
                                continue;
                            }
                            foreach ($element->childs as $child) {
                                if ($child->isFirstChildActive() or $child->active) {
                                    $element->active = true;
                                    return true;
                                }
                            }
                        }
                        return false;
                    }),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        $str = new Twig_SimpleFilter(
                'str_*', function ($name) {
            $arguments = array_slice(func_get_args(), 1);
            $name      = Str::camel($name);
            return call_user_func_array([$this->callbacks['str'], $name], $arguments);
        });
        $array = new Twig_SimpleFilter('array_*', function ($name) {
            $arguments = array_slice(func_get_args(), 1);
            return call_user_func_array([$this->callbacks['arr'], $name], $arguments);
        });
        $serializable = new Twig_SimpleFilter('serializable', function ($value) {
            try {
                $unserialized = unserialize($value);
                echo '<pre>';
                print_r($unserialized);
                echo '</pre>';
                return;
            } catch (Exception $ex) {
                return $value;
            }
        });
        $jsonable = new Twig_SimpleFilter('jsonable', function ($value) {
            try {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo str_replace(['Array', '(', ')'], ['', '[', ']'], print_r(array_dot($decoded), true));
                    return;
                }
                return $value;
            } catch (Exception $ex) {
                return $value;
            }
        });
        $stringify = new Twig_SimpleFilter('stringify', function ($params) {
            if (empty($params)) {
                return '';
            }
            $return = [];
            foreach ($params as $key => $value) {
                array_push($return, $key . '=' . JavaScriptDecorator::decorate($value));
            }
            echo implode(' ', $return);
            return '';
        });

        return [$str, $array, $serializable, $stringify, $jsonable];
    }

    /**
     * {@inheritDoc}
     */
    public function getTests()
    {
        return [
            'instanceof' => new Twig_Function_Method($this, 'isInstanceof')
        ];
    }

    /**
     * @param $var
     * @param $instance
     * @return bool
     */
    public function isInstanceof($var, $instance)
    {
        return $var instanceof $instance;
    }

}
