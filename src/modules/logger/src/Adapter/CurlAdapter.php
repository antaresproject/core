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

use Illuminate\Contracts\Console\Kernel as KernelContract;
use Illuminate\Contracts\Foundation\Application;
use Exception;
use CURLFile;

class CurlAdapter
{

    /**
     * kernel console instance
     *
     * @var array
     */
    protected $kernelConsole;

    /**
     * url with exception
     *
     * @var String 
     */
    protected $url;

    /**
     * additional description from user
     *
     * @var String
     */
    protected $description;

    /**
     * exception details
     *
     * @var String
     */
    protected $src;

    /**
     * component configuration container
     *
     * @var array
     */
    protected $config;

    /**
     * constructing
     * 
     * @param Application $app
     */
    public function __construct(Application $app, KernelContract $kernelConsole)
    {
        $this->config        = $app->make('config')->get('antares/logger::adapter.default');
        $this->kernelConsole = $kernelConsole;
    }

    /**
     * params setter
     * 
     * @param array $params
     * @return \Antares\Logger\Adapter\CurlAdapter
     * @throws Exception
     */
    public function setParams(array $params = array())
    {
        if (empty($params)) {
            throw new Exception('Unable to send exception. Invalid data provided.');
        }
        $this->description = isset($params['description']) ? $params['description'] : null;
        $this->url         = isset($params['url']) ? $params['url'] : null;
        $this->src         = isset($params['src']) ? $params['src'] : null;
        return $this;
    }

    /**
     * sends exception details to support system
     * 
     * @return \Antares\Logger\Adapter\CurlAdapter
     * @throws Exception
     */
    public function send()
    {
        $url = array_get($this->config, 'url');
        if (is_null($url)) {
            throw new Exception('Unable to find valid url. Please verify component configuration.');
        }


        $files = $this->getFiles();

        $token = csrf_token();


        $fields = array_merge([
            'url'         => $this->url,
            'description' => $this->description,
            'user_id'     => auth()->user()->id,
            'brand_id'    => app('antares.memory')->make('primary')->get('brand.default'),
            '_token'      => $token], $files);


        $headers[] = 'X-CSRF-Token:' . $token;



        $ch     = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $this->cleanup($files);
        if (!$this->isJsonResponse($result)) {
            throw new Exception('Unable to send exception details to support system.');
        }
        $response = json_decode($result);

        if (isset($response->id) && (int) $response->id <= 0) {
            throw new Exception('Unable to send exception details to support system.');
        }
        return $this;
    }

    /**
     * check whether response from support system has json structure
     * 
     * @param type $string
     * @return type
     */
    protected function isJsonResponse($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * deletes files from temporary directory
     * 
     * @param array $files
     * @return \Antares\Logger\Adapter\CurlAdapter
     */
    protected function cleanup(array $files = array())
    {
        if (empty($files)) {
            return $this;
        }
        foreach ($files as $file) {
            if (!file_exists($file->name)) {
                continue;
            }
            unlink($file->name);
        }
        return $this;
    }

    /**
     * get list of files to transfer report exception
     * 
     * @return array
     */
    protected function getFiles()
    {

        $filename = storage_path('temp') . DIRECTORY_SEPARATOR . str_random() . '.html';

        file_put_contents($filename, $this->src);
        $files              = [$filename];
        $this->kernelConsole->call('report:analyzer');
        $analyzerReportFile = trim($this->kernelConsole->output());
        if (file_exists($analyzerReportFile)) {
            array_push($files, $analyzerReportFile);
        }
        $return = [];

        foreach ($files as $index => $file) {
            $return["files[{$index}]"] = new CURLFile($file);
        }
        return $return;
    }

}
