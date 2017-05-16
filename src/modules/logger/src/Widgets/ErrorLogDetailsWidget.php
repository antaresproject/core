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

namespace Antares\Logger\Widgets;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Antares\Logger\Utilities\LogViewer;
use Exception;

class ErrorLogDetailsWidget extends AbstractTemplate
{

    /**
     * name of widget
     * 
     * @var String 
     */
    public $name = 'Error Log Details Widget';

    /**
     * LogViewer instance
     *
     * @var LogViewer 
     */
    protected $viewer;

    /**
     *
     * @var type 
     */
    protected $attributes = [
        'titlable' => true
    ];

    /**
     * Where widget should be available 
     *
     * @var array
     */
    protected $views = [
        'antares/logger::admin.index.details'
    ];

    /**
     * constructing
     * 
     * @param LogViewer $viewer
     */
    public function __construct(LogViewer $viewer)
    {
        parent::__construct();
        $this->viewer = $viewer;
        $this->name   = trans('antares/logger::messages.error_log_details_widget_title', ['date' => from_route('date')]);
    }

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        if (!is_null($date = from_route('date'))) {
            $level   = from_route('level');
            $log     = $this->getLogOrFail($date);
            $levels  = $this->viewer->levelsNames();
            $entries = $log->entries(is_null($level) ? 'all' : $level);
            return view('antares/logger::admin.widgets.error_details_widget', compact('log', 'levels', 'entries', 'level'));
        }
        return '';
    }

    /**
     * Get a log or fail
     *
     * @param  string  $date
     *
     * @return Log|null
     */
    private function getLogOrFail($date)
    {
        try {
            return $this->viewer->get($date);
        } catch (Exception $e) {
            return abort(404, $e->getMessage());
        }
    }

}
