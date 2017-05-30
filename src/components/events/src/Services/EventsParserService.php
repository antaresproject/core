<?php

namespace Antares\Events\Services;

use Symfony\Component\Finder\Finder;
use Antares\Events\Model\Event as EventModel;

/**
 * Class EventsParserService
 * @package Antares\Events\Services
 */
class EventsParserService
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $interfaceName;

    /**
     * @var array
     */
    private $eventsList = [];

    /**
     * EventsParserService constructor.
     * @param string $path
     * @param string $interfaceName
     */
    public function __construct(string $path, string $interfaceName)
    {
        $this->path = rtrim($path, DS) . DS . 'src';
        $this->interfaceName = $interfaceName;
        self::prepareEventsList();
    }


    /**
     *
     */
    public function parse()
    {
        self::updateDatabase();
    }

    /**
     *
     */
    public function prepareEventsList()
    {
        $finder = new Finder();
        $finder->files()->path('src' . DS . 'Events')->name('*.php')->in($this->path);
        foreach ($finder as $file) {
            try {
                require_once $file->getPathname();
            } catch (Exception $exc) {
                \Illuminate\Support\Facades\Log::warning($exc);
            }
        }
        foreach (get_declared_classes() as $class) {
            if (get_parent_class($class) === $this->interfaceName) {
                $this->eventsList[$class] = $class;
            }
        }
    }

    /**
     *
     */
    public function updateDatabase()
    {
        $events = $this->eventsList;
        $databaseEvents = EventModel::all();
        foreach ($databaseEvents as $databaseEvent) {
            if (!in_array($databaseEvent->namespace, $events)) {
                $databaseEvent->delete();
            } else {
                unset($events[$databaseEvent->namespace]);
            }
        }
        foreach ($events as $event) {
            (new EventModel())->fill(['namespace' => $event])->save();
        }
    }
}