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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



namespace Antares\Logger\Utilities;

use Antares\Support\Facades\HTML;
use ReflectionClass;

class LogDecorator
{

    /**
     * Locale code for translations
     *
     * @var String
     */
    protected $locale = null;

    /**
     * Keeps model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model = null;

    /**
     * Notifications contents instance
     *
     * @var \Antares\Notifications\Contents
     */
    protected $notificationContents = null;

    /**
     * Constructing
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    public function __construct($model)
    {
        $this->model                = $model;
        $this->notificationContents = app('notifications.contents');
    }

    /**
     * Inititate params container
     * 
     * @param array $params
     * @return String
     */
    protected function initiate(&$params)
    {
        $name   = $this->model->name;
        $owner  = isset($this->model->new_value['name']) ? $this->model->new_value['name'] : $this->model->owner_id;
        $user   = is_null($this->model->user) ? 'Automation' : anchor('users/' . $this->model->user_id, '#' . $this->model->user->id . ' ' . $this->model->user->fullname);
        $params = [
            'owner_id' => $owner,
            'user_id'  => $this->model->user_id,
            'user'     => $user
        ];
        $params = array_merge($params, (array) $this->model->additional_params);
        return $name;
    }

    /**
     * Locale setter 
     * 
     * @param String $locale
     * @return \Antares\Logger\Utilities\LogDecorator
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Decorates log entry
     * 
     * @return String
     */
    public function decorate()
    {
        $classname = $this->model->owner_type;
        $return    = '';
        if ($this->hasMethod($classname, 'getLogTitle')) {
            $return = $classname::getLogTitle($this->model->owner_id, $this->model);
        }
        if (strlen($return)) {
            return $return;
        }
        list($operation, $params) = $this->getOperationWithParams();
        return $this->translate($operation, $params);
    }

    /**
     * Gets operation name with params
     * 
     * @return array
     */
    protected function getOperationWithParams()
    {
        $params    = [];
        $operation = $this->initiate($params);
        if ($operation == 'JOBRESULTS_CREATED') {
            $operation = $this->job($params);
        } else {

            $found = false;
            $url   = $this->getUrlPattern();
            $map   = [];
            if (!is_null($this->model->new_value)) {
                $map    = $this->getReplacementKeys();
                $values = $this->getReplacementValues();
                $title  = $this->getTitle($this->model->new_value);
                $found  = $this->urlFillable($url, $map);
            } else {
                $title = $this->getTitle($this->model->old_value);
            }
            array_set($params, 'owner_id', ($found && strlen($url) > 0) ? $this->link($map, $values, $url, $title) : $title );

            $operation = $this->getRelationData($params, $map);
        }
        $this->fill($params);
        return [$operation, $params];
    }

    /**
     * Translates log message
     * 
     * @param String $operation
     * @param array $params
     * @return String
     */
    protected function translate($operation, array $params = [])
    {

        $locale     = is_null($this->locale) ? app()->getLocale() : $this->locale;
        $translator = app('translator');
        if (($name       = $this->notificationContents->find($operation, $locale)) !== false) {
            return $translator->transWith($name, $params);
        }
        return $translator->trans("antares/logger::operations.{$operation}", $params, 'messages', $locale, false);
    }

    /**
     * Gets raw message before translation
     * 
     * @return array
     */
    public function raw()
    {
        return $this->getOperationWithParams();
    }

    /**
     * Gets relation data for log entry
     * 
     * @param array $params
     * @param array $map
     * @return string
     */
    protected function getRelationData(&$params, array $map = [])
    {
        $operation = $this->model->name;

        if (is_null($this->model->related_data)) {
            return $operation;
        }
        foreach ($this->model->related_data as $relation => $data) {

            if ($relation === 'users') {
                $classname  = get_class(\Antares\Support\Facades\Foundation::make('antares.user'));
                $urlPattern = $this->getUrlPattern($classname);
                $links      = [];
                foreach ($data as $element) {
                    $links[] = $this->link('{id}', $element['id'], $urlPattern, array_get($element, 'firstname') . ' ' . array_get($element, 'lastname'));
                }
                $params[$relation] = implode(', ', $links);
            } elseif ($relation === 'user') {
                continue;
            } elseif (!empty($data)) {
                foreach ($data as $classname => $values) {
                    if (empty($values)) {
                        continue;
                    }
                    if (!class_exists($classname)) {
                        continue;
                    }
                    if (($url = $this->getUrlPattern($classname)) !== '') {
                        $submap    = $this->getReplacementKeys($values);
                        $subValues = array_values($values);
                        $title     = array_get($values, 'name', array_get($values, 'id'));
                        array_set($params, $relation, !$this->urlFillable($url, $map) ? $title : $this->link($submap, $subValues, $url, $title) );
                    } else {
                        $params[$relation] = $this->getTitle(current($data));
                    }
                }
            }
            $operation .= '_' . strtoupper($relation);
        }

        return $operation;
    }

    /**
     * Generates log link
     * 
     * @param array $keys
     * @param array $replacements
     * @param String $url
     * @param String $title
     * @return String
     */
    protected function link($keys, $replacements, $url, $title)
    {
        return HTML::link(handles(str_replace($keys, $replacements, $url)), $title);
    }

    /**
     * Gets replacement values
     * 
     * @return array
     */
    protected function getReplacementValues()
    {
        return array_values($this->model->new_value);
    }

    /**
     * Gets replacement keys
     * 
     * @param array $map
     * @return String
     */
    protected function getReplacementKeys(array $map = [])
    {
        $keys = empty($map) ? array_keys($this->model->new_value) : array_keys($map);
        return array_map(function($current) {
            return '{' . $current . '}';
        }, $keys);
    }

    /**
     * Gets url pattern when available
     * 
     * @param String $class
     * @return String
     */
    protected function getUrlPattern($class = null)
    {
        $url       = '';
        $classname = is_null($class) ? $this->model->owner_type : $class;
        if (!class_exists($classname)) {
            return $url;
        }
        if ($this->hasMethod($classname, 'getPatternUrl')) {
            $url = $classname::getPatternUrl($this->model->owner_id, $this->model);
        }
        return $url;
    }

    /**
     * Gets url fillable params
     * 
     * @param String $url
     * @param array $map
     * @return boolean
     */
    protected function urlFillable($url, array $map = [])
    {
        if (!str_contains($url, '{') and ! str_contains($url, '}')) {
            return true;
        }
        foreach ($map as $value) {
            if (str_contains($url, $value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether class has provided method
     * 
     * @param String $classname
     * @param String $method
     * @return boolean
     */
    protected function hasMethod($classname, $method)
    {
        $reflection = new ReflectionClass($classname);
        return $reflection->hasMethod($method);
    }

    /**
     * Gets title
     * 
     * @param array $from
     * @return String
     */
    protected function getTitle(array $from = null)
    {
        $title = null;
        if (array_has($from, 'firstname') && array_has($from, 'lastname')) {
            return '#' . implode(' ', array_only($from, ['id', 'firstname', 'lastname']));
        }
        foreach (['name', 'word', 'value', 'email', 'version', 'domain', 'message', 'hostname', 'company_name', 'ip_address', 'id'] as $internalKey) {
            if (!is_null($title = array_get($from, $internalKey))) {
                break;
            }
        }
        return is_null($title) ? $this->model->owner_id : $title;
    }

    /**
     * When logs entity comes from automation job
     * 
     * @param array $params
     * @return String
     */
    protected function job(&$params)
    {
        $operationName = $this->model->name;
        $instance      = app($this->model->owner_type)->with('job')->find($this->model->owner_id);
        if (is_null($instance)) {
            array_set($params, 'time', '0.0');
            array_set($params, 'name', '[not available]');
            $params['return'] = '<span class="label-basic label-basic--danger ">Error</span>';
            return 'JOBRESULTS_CREATED_ERROR';
        }
        array_set($params, 'return', $instance->return);
        array_set($params, 'time', $instance->runtime);
        array_set($params, 'name', anchor(handles('antares::automation/show/' . $instance->job_id), $instance->job->name));

        if (array_get($this->model->new_value, 'has_error') !== false) {
            $operationName    = 'JOBRESULTS_CREATED_ERROR';
            $params['return'] = '<span id="error-' . $this->model->owner_id . '" class="label-basic label-basic--danger ">error</span><div class="mdl-tooltip" for="error-' . $this->model->owner_id . '">error</div>';
        } else {
            $params['return'] = '<span id="success-' . $this->model->owner_id . '" class="label-basic label-basic--success ">SUCCESSFULLY</span><div class="mdl-tooltip" for="success-' . $this->model->owner_id . '">successfully</div>';
        }
        return $operationName;
    }

    /**
     * Fills params with rest of entity columns
     * 
     * @param array $params
     */
    protected function fill(&$params)
    {
        if (is_null($this->model->new_value)) {
            return false;
        }
        foreach ($params as $index => $value) {
            if (isset($params[$index])) {
                continue;
            }
            $params[$index] = array_get($this->model->new_value, $index);
        }
        return $params;
    }

}
