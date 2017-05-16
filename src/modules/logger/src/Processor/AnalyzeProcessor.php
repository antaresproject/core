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



namespace Antares\Logger\Processor;

use Antares\Logger\Contracts\AnalyzePresenter as Presenter;
use Antares\Logger\Http\Presenters\SystemInfo;
use Antares\Logger\Adapter\ChecksumAdapter;
use Antares\Foundation\Processor\Processor;
use Antares\Logger\Adapter\ServerAdapter;
use Antares\Logger\Adapter\LogsAdapter;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Linfo\Linfo;

class AnalyzeProcessor extends Processor
{

    /**
     * list of analyzer available actions
     *
     * @var array 
     */
    protected $actions;

    /**
     * instance of filesystem
     *
     * @var Filesystem 
     */
    protected $filesystem;

    /**
     * checksum adapter instance
     *
     * @var ChecksumAdapter 
     */
    protected $checksumAdapter;

    /**
     * logs adapter instance
     *
     * @var LogsAdapter 
     */
    protected $logsAdapter;

    /**
     * server adapter instance
     *
     * @var ServerAdapter 
     */
    protected $serverAdapter;

    /**
     * constructing
     * 
     * @param Presenter $presenter
     * @param Filesystem $filesystem
     * @param ChecksumAdapter $checksumAdapter
     * @param LogsAdapter $logsAdapter
     */
    public function __construct(Presenter $presenter, Filesystem $filesystem, ChecksumAdapter $checksumAdapter, LogsAdapter $logsAdapter, ServerAdapter $serverAdapter)
    {
        $this->presenter       = $presenter;
        $this->actions         = config('antares/logger::analyzer.actions');
        $this->filesystem      = $filesystem;
        $this->checksumAdapter = $checksumAdapter;
        $this->logsAdapter     = $logsAdapter;
        $this->serverAdapter   = $serverAdapter;
    }

    /**
     * default index action
     * 
     * @return type
     */
    public function index()
    {
        $urls = [];
        foreach ($this->actions as $action => $description) {
            $fired = \Illuminate\Support\Facades\Event::fire('analyzer:before.' . $action);
            if (!empty($fired)) {
                array_push($urls, current($fired));
            }
            array_push($urls, [
                'url'         => handles('antares::logger/analyze/' . $action, ['csrf' => true]),
                'description' => $description,
            ]);
        }
        return new JsonResponse($urls);
    }

    /**
     * read server environment
     * 
     * @return \Illuminate\View\View
     */
    public function server()
    {
        $data = $this->serverAdapter->verify();

        return $this->presenter->server($data);
    }

    /**
     * read system environment
     * 
     * @return \Illuminate\View\View
     */
    public function system()
    {
        $linfo  = new Linfo(config('antares/logger::analyzer.system'));
        $linfo->scan();
        $output = new SystemInfo($linfo);
        $info   = $output->output();
        return $this->presenter->system($info);
    }

    /**
     * read modules list 
     * 
     * @return \Illuminate\View\View
     */
    public function modules()
    {
        $predefinedDirectories = ['core', 'components'];
        $return                = [];
        foreach ($predefinedDirectories as $predefinedDirectory) {
            $directoryPath = base_path("src/{$predefinedDirectory}");
            $directories   = $this->filesystem->directories($directoryPath);
            foreach ($directories as $directory) {
                $size  = 0;
                $files = $this->filesystem->allFiles($directory);
                foreach ($files as $file) {
                    $size += $file->getSize();
                }
                $return[$predefinedDirectory][] = [
                    'directory'   => $predefinedDirectory . DIRECTORY_SEPARATOR . last(explode(DIRECTORY_SEPARATOR, $directory)),
                    'files_count' => count($files),
                    'size'        => $this->humanFileSize($size, 'MB'),
                ];
            }
        }
        return $this->presenter->modules($return);
    }

    /**
     * get system version
     * 
     * @return \Illuminate\View\View
     */
    public function version()
    {
        $adapter = app('antares.version')->getAdapter();
        $data    = ['version' => $adapter->getActualVersion()];
        if ($adapter->isNewer()) {
            $data['messages'][] = ['warning', sprintf('New System version available. We strongly recommend upgrade to version %s', $adapter->getVersion())];
        }
        return $this->presenter->version($data);
    }

    /**
     * report database tables 
     * 
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $tables = DB::select('SHOW TABLE STATUS');
        return $this->presenter->database($tables);
    }

    /**
     * get informations about logs
     * 
     * @return \Illuminate\View\View
     */
    public function logs()
    {
        $data = $this->logsAdapter->verify();
        return $this->presenter->logs($data);
    }

    /**
     * report installed components
     * 
     * @return \Illuminate\View\View
     */
    public function components()
    {
        $ignore     = ['addons/playground', 'components/tickets', 'domains/dns'];
        $extensions = app('antares.memory')->make('component');
        $active     = array_except($extensions->get('extensions.active'), $ignore);

        $available = array_except($extensions->get('extensions.available'), $ignore);
        $inactive  = array_flip(array_diff(array_keys($available), array_keys($active)));
        $data      = [
            'components' => $available,
            'inactive'   => $inactive
        ];
        return $this->presenter->components($data);
    }

    /**
     * checksum of system files report
     * 
     * @return \Illuminate\View\View
     */
    public function checksum()
    {
        $data = $this->checksumAdapter->verify();
        return $this->presenter->checksum($data);
    }

    /**
     * get file size with units
     * 
     * @param mixed $size
     * @param String $unit
     * @return String
     */
    function humanFileSize($size, $unit = "")
    {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB") {
            return number_format($size / (1 << 30), 2) . "GB";
        }
        if ((!$unit && $size >= 1 << 20) || $unit == "MB") {
            return number_format($size / (1 << 20), 2) . "MB";
        }
        if ((!$unit && $size >= 1 << 10) || $unit == "KB") {
            return number_format($size / (1 << 10), 2) . "KB";
        }
        return number_format($size) . " bytes";
    }

}
