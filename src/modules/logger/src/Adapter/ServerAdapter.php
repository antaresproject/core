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



namespace Antares\Logger\Adapter;

class ServerAdapter
{

    /**
     * messages container
     *
     * @var array
     */
    protected $messages = [];

    /**
     * configuration container;
     *
     * @var array 
     */
    protected $configuration = [];

    /**
     * important php extensions
     *
     * @var array 
     */
    protected static $importantExtensions = [
        'curl', 'mcrypt', 'zip', 'openssl', 'gd', 'mbstring', 'Zend OPcache'
    ];

    /**
     * minimal php ini configuration requirements 
     *
     * @var array 
     */
    protected static $iniRequirements = [
        'max_execution_time'  => 300,
        'max_file_uploads'    => 10,
        'max_input_time'      => 300,
        'post_max_size'       => '100M',
        'upload_max_filesize' => '100M'
    ];

    /**
     * verify server environment
     * 
     * @return array 
     */
    public function verify()
    {
        $phpversion = phpversion();
        $extensions = get_loaded_extensions();
        $ini        = array_only(ini_get_all(), ['max_execution_time', 'max_file_uploads', 'max_input_time', 'post_max_size', 'upload_max_filesize']);

        $return              = [
            'version'    => $phpversion,
            'transports' => implode(', ', stream_get_transports()),
            'filters'    => implode(', ', stream_get_filters()),
            'doc_root'   => getcwd(),
            'extensions' => $extensions,
            'ini'        => $ini,
        ];
        $this->configuration = $return;
        $this->validate($return);
        return $return;
    }

    /**
     * validates server environment
     * 
     * @param array $data
     * @return array
     */
    protected function validate(array &$data)
    {
        $this->validateVersion();
        $this->validateExtensions();
        $this->validateIni();
        $data = array_merge($data, ['messages' => $this->messages]);
        return $data;
    }

    /**
     * validates stream filters
     * 
     * @return \Antares\Logger\Adapter\ServerAdapter
     */
    protected function validateFilters()
    {
        return $this;
    }

    /**
     * validates registered stream transport
     * 
     * @return \Antares\Logger\Adapter\ServerAdapter
     */
    protected function validateTransports()
    {
        return $this;
    }

    /**
     * php ini validator
     * 
     * @return \Antares\Logger\Adapter\ServerAdapter
     */
    protected function validateIni()
    {
        $data = $this->configuration;
        if (!isset($data['ini'])) {
            array_push($this->messages, ['error', trans('Unable to read php ini configuration. It is recommended to contact with your provider.')]);
            return $this;
        }
        if (empty(self::$iniRequirements)) {
            return $this;
        }
        $ini = $data['ini'];

        foreach (self::$iniRequirements as $name => $value) {
            if (!isset($ini[$name])) {
                array_push($this->messages, ['error', sprintf('Option (%s) not exists in php ini configuration.', $name)]);
            } else {
                if ($ini[$name]['global_value'] != $value) {
                    array_push($this->messages, ['warning', sprintf('Option (%s) is not same with minimal requirement. Found %s expected %s.', $name, $ini[$name]['global_value'], $value)]);
                }
            }
        }
        return $this;
    }

    /**
     * php extensions validator
     * 
     * @return \Antares\Logger\Adapter\ServerAdapter
     */
    protected function validateExtensions()
    {
        $data = $this->configuration;

        if (!isset($data['extensions'])) {
            array_push($this->messages, ['error', trans('Unable to read php extensions. It is recommended to contact with your provider.')]);
            return $this;
        }
        if (empty(self::$importantExtensions)) {
            return $this;
        }
        $extensions = $data['extensions'];
        foreach (self::$importantExtensions as $extension) {
            if (!in_array($extension, $extensions)) {
                array_push($this->messages, ['warning', sprintf('Unable to find %s php extension. Please install extension on your server otherwise system may work not properly.', $extension)]);
            }
        }
        return $this;
    }

    /**
     * php version validator
     * 
     * @return \Antares\Logger\Adapter\ServerAdapter
     */
    protected function validateVersion()
    {
        $data = $this->configuration;

        $matches = [];
        if (!isset($data['version']) or ! preg_match('/^([0-9]).*$/i', $data['version'], $matches)) {
            array_push($this->messages, ['error', trans('Unable to read php version. It is recommended to contact with your provider.')]);
            return $this;
        }
        if (!isset($matches[1])) {
            array_push($this->messages, ['error', trans('Unable to read php primary version. It is recommended to contact with your provider.')]);
        }
        if ((int) $matches[1] < 7) {
            array_push($this->messages, ['warning', sprintf('Current PHP Version (%s) does not match the minimum system requirements. It is recommended to use PHP Version 7.', $data['version'])]);
        }

        if (!preg_match('/^([0-9]).([0-9]).([0-9])$/', $data['version'])) {
            array_push($this->messages, ['warning', sprintf('Current PHP Version (%s) is not stable. Use only stable releases.', $data['version'])]);
        }
        return $this;
    }

}
