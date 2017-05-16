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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Updater\Widgets;

use Antares\UI\UIComponents\Adapter\AbstractTemplate;
use Antares\Support\Facades\Foundation;
use Antares\Updater\Model\Version;

class VersionWidget extends AbstractTemplate
{

    /**
     * Name of widget
     * 
     * @var String 
     */
    public $name = 'Version Widget';

    /**
     * Widget title
     * 
     * @var String 
     */
    public $title = 'System version';

    /**
     * widget attributes
     *
     * @var array
     */
    protected $attributes = [
        'x'              => 0,
        'y'              => 0,
        'min_width'      => 2,
        'min_height'     => 8,
        'max_width'      => 12,
        'max_height'     => 24,
        'default_width'  => 4,
        'default_height' => 8,
        'resizable'      => true,
        'draggable'      => true,
        'nestable'       => false,
        'titlable'       => true,
        'editable'       => false,
        'removable'      => true
    ];

    /**
     * render widget content
     * 
     * @return String | mixed
     */
    public function render()
    {
        $adapter    = app('antares.version')->getAdapter();
        $adapter->retrive();
        $updateUrl  = handles('antares::updater/update');
        $sandboxUrl = handles('antares::updater/sandbox');
        $version    = $adapter->getVersion();

        $actual  = $adapter->getNextVersion();
        $details = array_merge($this->details(), ['description' => $adapter->getDescription(), 'changeLog' => $adapter->getChangeLog(), 'isNewer' => $adapter->isNewer(), 'available' => $version, 'updateUrl' => $updateUrl, 'sandboxUrl' => $sandboxUrl, 'actual' => $actual]);

        $model = Foundation::make('Antares\Updater\Model\Sandbox')->where('version', $version)->first();

        if (!is_null($model)) {
            array_set($details, 'sandbox', handles('antares::updater/update', ['csrf' => true, 'sandbox' => $version]));
        }

        return view('antares/updater::widgets.version', $details);
    }

    /**
     * get information about current system version
     * 
     * @return array
     */
    protected function details()
    {
        return Version::actual()->first()->toArray();
    }

}
