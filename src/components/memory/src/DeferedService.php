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

namespace Antares\Memory;

use Antares\Memory\Model\DeferedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\DB;

class DeferedService
{

    /**
     * fires all defered events
     * 
     * @return boolean
     */
    public function run()
    {
        if (!$this->validate()) {
            return false;
        }

        return DB::transaction(function() {
                    $events = $this->getDeferedEvents();
                    foreach ($events as $event) {
                        if (!empty($event->value)) {
                            continue;
                        }
                        $return       = Event::fire($event->name);
                        $event->value = $return;
                        $event->save();
                    }
                });
    }

    /**
     * get all defered events
     * 
     * @return \Illuminate\Support\Collection
     */
    public function getDeferedEvents()
    {
        return DeferedEvent::all();
    }

    /**
     * validated whether service can be runned
     * 
     * @return boolean
     */
    protected function validate()
    {
        return (php_sapi_name() !== 'cli') and app('antares.installed');
    }

}
