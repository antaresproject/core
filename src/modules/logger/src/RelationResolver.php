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



namespace Antares\Logger;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;
use Antares\Support\Str;
use ReflectionMethod;
use SplFileObject;
use Exception;

class RelationResolver
{

    /**
     * Model relations
     *
     * @var array
     */
    private $properties = [];

    /**
     * Model methods
     *
     * @var array
     */
    protected $methods = array();

    /**
     * Gets relations from model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function getRelations($model)
    {
        $methods = get_class_methods($model);
        if (!$methods) {
            return [];
        }
        foreach ($methods as $method) {
            if (Str::startsWith($method, 'get') && Str::endsWith($method, 'Attribute') && $method !== 'getAttribute') {
                $name = Str::snake(substr($method, 3, -9));
                if (!empty($name)) {
                    $this->setProperty($name, null, true, null);
                }
            } elseif (Str::startsWith($method, 'set') && Str::endsWith($method, 'Attribute') && $method !== 'setAttribute') {
                $name = Str::snake(substr($method, 3, -9));
                if (!empty($name)) {
                    $this->setProperty($name, null, null, true);
                }
            } elseif (Str::startsWith($method, 'scope') && $method !== 'scopeQuery') {
                $name = Str::camel(substr($method, 5));
                if (!empty($name)) {
                    $reflection = new \ReflectionMethod($model, $method);
                    $args       = $this->getParameters($reflection);
                    array_shift($args);
                    $this->setMethod($name, '\Illuminate\Database\Query\Builder|\\' . $reflection->class, $args);
                }
            } elseif (!method_exists('Illuminate\Database\Eloquent\Model', $method) && !Str::startsWith($method, 'get')) {
                $reflection = new ReflectionMethod($model, $method);
                $file       = new SplFileObject($reflection->getFileName());
                $file->seek($reflection->getStartLine() - 1);
                $code       = '';
                while ($file->key() < $reflection->getEndLine()) {
                    $code .= $file->current();
                    $file->next();
                }
                $code  = trim(preg_replace('/\s\s+/', '', $code));
                $begin = strpos($code, 'function(');
                $code  = substr($code, $begin, strrpos($code, '}') - $begin + 1);

                foreach (array('hasOne', 'belongsTo') as $relation) {
                    $search = '$this->' . $relation . '(';
                    if ($pos    = stripos($code, $search)) {
                        $relationObj = $model->$method();
                        if ($relationObj instanceof Relation) {
                            $relatedModel = '\\' . get_class($relationObj->getRelated());

                            $relations = ['hasManyThrough', 'belongsToMany', 'hasMany', 'morphMany', 'morphToMany'];
                            if (in_array($relation, $relations)) {
                                $this->setProperty($method, $this->getCollectionClass($relatedModel) . '|' . $relatedModel . '[]', true, null);
                            } elseif ($relation === "morphTo") {
                                $this->setProperty($method, '\Illuminate\Database\Eloquent\Model|\Eloquent', true, null);
                            } else {
                                $this->setProperty($method, $relatedModel, true, null);
                            }
                        }
                    }
                }
            }
        }
        return $this->properties;
    }

    /**
     * Determine a model classes' collection type.
     *
     * @param string $className
     * @return string
     */
    private function getCollectionClass($className)
    {
        if (!method_exists($className, 'newCollection')) {
            return '\Illuminate\Database\Eloquent\Collection';
        }
        $model = new $className;
        return '\\' . get_class($model->newCollection());
    }

    /**
     * Model property setter
     *
     * @param string $name
     * @param string|null $type
     * @param bool|null $read
     * @param bool|null $write
     * @param string|null $comment
     */
    protected function setProperty($name, $type = null, $read = null, $write = null, $comment = '')
    {
        if (!isset($this->properties[$name])) {
            $this->properties[$name]            = array();
            $this->properties[$name]['type']    = 'mixed';
            $this->properties[$name]['read']    = false;
            $this->properties[$name]['write']   = false;
            $this->properties[$name]['comment'] = (string) $comment;
        }
        if ($type !== null) {
            $this->properties[$name]['type'] = $type;
        }
        if ($read !== null) {
            $this->properties[$name]['read'] = $read;
        }
        if ($write !== null) {
            $this->properties[$name]['write'] = $write;
        }
    }

    /**
     * Gets prepared data from related model
     *
     * @param Model $model
     * @return array
     */
    public function getRelationData(Model $model)
    {
        $relations = $this->getRelations($model);
        if (empty($relations)) {
            return [];
        }
        $return = [];

        foreach ($this->properties as $name => $details) {
            try {
                $related = $model->{$name};
                if (!is_object($model->$name())) {
                    continue;
                }
            } catch (Exception $ex) {
                continue;
            }

            if (is_null($related)) {
                $related = $model->$name()->getModel();
                array_set($return, $name, [get_class($related) => $related->getFillable()]);
            }
            if (!is_null($related) && method_exists($related, 'getAttributes')) {
                array_set($return, $name, [get_class($related) => $related->getAttributes()]);
            }
        }
        return $return;
    }

    /**
     * Get the parameters and format them correctly
     *
     * @param $method
     * @return array
     */
    public function getParameters($method)
    {
        //Loop through the default values for paremeters, and make the correct output string
        $params            = array();
        $paramsWithDefault = array();
        /** @var \ReflectionParameter $param */
        foreach ($method->getParameters() as $param) {
            $paramStr = '$' . $param->getName();
            $params[] = $paramStr;
            if ($param->isOptional() && $param->isDefaultValueAvailable()) {
                $default = $param->getDefaultValue();
                if (is_bool($default)) {
                    $default = $default ? 'true' : 'false';
                } elseif (is_array($default)) {
                    $default = 'array()';
                } elseif (is_null($default)) {
                    $default = 'null';
                } elseif (is_int($default)) {
                    //$default = $default;
                } else {
                    $default = "'" . trim($default) . "'";
                }
                $paramStr .= " = $default";
            }
            $paramsWithDefault[] = $paramStr;
        }
        return $paramsWithDefault;
    }

    protected function setMethod($name, $type = '', $arguments = array())
    {
        $methods = array_change_key_case($this->methods, CASE_LOWER);
        if (!isset($methods[strtolower($name)])) {
            $this->methods[$name]              = array();
            $this->methods[$name]['type']      = $type;
            $this->methods[$name]['arguments'] = $arguments;
        }
    }

}
