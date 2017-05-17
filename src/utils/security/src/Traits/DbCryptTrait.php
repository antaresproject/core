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

namespace Antares\Security\Traits;

use Antares\Extension\FilesystemFinder;
use Illuminate\Support\Facades\Schema;
use Antares\Security\Database\Cryptor;
use ReflectionClass;

trait DbCryptTrait
{

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * crypt config 
     *
     * @var array
     */
    private $columnTypes = [];

    /**
     * whether cryptor is enabled
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * name of cast
     *
     * @var String
     */
    private $castName = null;

    /**
     * 
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->setCasting();
    }

    /**
     * package name getter
     * 
     * @return String
     */
    private function getPackage()
    {
        $reflection = new ReflectionClass(get_called_class());
        return app()->make(FilesystemFinder::class)->resolveNamespace($reflection->getFileName(), true);
    }

    /**
     * fills configuration options
     * 
     * @return boolean
     */
    private function setCasting()
    {
        $package   = $this->getPackage();
        $tablename = $this->getTable();
        $config    = config("db_cryptor", []);
        if (!array_get($config, 'enabled')) {
            return false;
        }
        if (is_null($castName = array_get($config, 'cast_name'))) {
            return false;
        }
        $this->castName = $castName;
        if (is_null($columnTypes    = array_get($config, 'column_types'))) {
            return false;
        }
        $this->columnTypes = $columnTypes;
        $this->enabled     = true;
        $casts             = config($package . "::cast.{$tablename}", []);


        $this->setCast($casts);
    }

    /**
     * casts setter
     * 
     * @param array $casts
     * @return void
     */
    private function setCast($casts)
    {
        foreach ($casts as $cast) {
            if (isset($this->casts[$cast])) {
                continue;
            }
            $this->casts[$cast] = $this->castName;
        }
        return;
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        $return    = parent::castAttribute($key, $value);
        $tablename = $this->getTable();
        if (!$this->validate($key, $tablename)) {
            return $return;
        }
        return Cryptor::getInstance()->crypt('decrypt', $return);
    }

    /**
     * validates whether attributes need to be encrypted / decrypted
     * 
     * @param type $key
     * @param type $tablename
     * @return boolean
     */
    protected function validate($key, $tablename)
    {
        if (!$this->enabled) {
            return false;
        }
        $castType = $this->getCastType($key);
        if ($castType !== $this->castName) {
            return false;
        }
        if (!$this->isAesCastable($key, $tablename)) {
            return false;
        }
        if (!in_array(Schema::getColumnType($tablename, $key), $this->columnTypes)) {
            return false;
        }
        return true;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);
        if ($this->isAesCastable($key, $this->getTable())) {
            $this->attributes[$key] = Cryptor::getInstance()->crypt('encrypt', $value);
        }
        return $this;
    }

    /**
     * Determine whether a value is JSON castable for inbound manipulation.
     *
     * @param  string  $key
     * @return bool
     */
    protected function isAesCastable($key)
    {
        return array_key_exists($key, $this->casts) and $this->casts[$key] == $this->castName;
    }

}
