<?php

namespace Antares\Foundation\Events;

use Antares\Foundation\Events\Contract\Event;
use Antares\Foundation\Events\Model\Event as EventModel;

abstract class AbstractEvent implements Event
{

    /** @var string */
    protected static $name;

    /** @var  string */
    protected static $description;

    /** @var bool */
    protected static $isCountable = true;

    /**
     * @return string
     */
    public static function getName(): string
    {
        return (string) static::$name;
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return (string) static::$description;
    }

    /**
     * @return bool
     */
    public static function isCountable(): bool
    {
        return (bool) static::$isCountable;
    }

    /**
     * @return EventModel
     */
    protected function model(): EventModel
    {
        $model = app(EventModel::class);
        $namespace = get_class($this);
        $model->namespace = $namespace;

        return ($event = $model->where('namespace', $namespace)->first()) instanceof EventModel
            ? $event : $model;
    }

    /**
     * Event constructor
     */
    public function __construct()
    {
        $model = $this->model();

        $fireCount = $model->fire_count ?? 0;

        if (static::isCountable()) {
            $fireCount++;
        }

        $details = debug_backtrace()[1] ?? null;

        $model->details = $details ? serialize([
            'file'     => $details['file'],
            'line'     => $details['line'],
            'function' => $details['function']
        ]) : null;
        $model->fire_count = $fireCount;
        $model->save();
    }

}
