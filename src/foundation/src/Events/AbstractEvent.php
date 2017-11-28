<?php

namespace Antares\Foundation\Events;

use Antares\Foundation\Events\Contract\Event;
use Antares\Foundation\Events\Model\Event as EventModel;

abstract class AbstractEvent implements Event
{

    /** @var string */
    protected static $name;

    /** @var string */
    protected static $description;

    /** @var bool */
    protected static $isCountable = true;

    /**
     * Event constructor
     */
    public function __construct()
    {
        if (!app()->bound('antares.installed')) {
            return;
        }
        if (!app('antares.installed')) {
            return;
        }
        if (!config('antares/foundation::log_events')) {
            return;
        }

        $model     = $this->model();
        $fireCount = $model->fire_count ?? 0;

        if (static::isCountable()) {
            $fireCount++;
        }

        $details       = debug_backtrace()[1] ?? null;
        $listenersData = $this->collectListenersData($model->namespace);

        $model->details    = $details ? serialize([
                    'file'      => $details['file'] ?? null,
                    'line'      => $details['line'] ?? null,
                    'function'  => $details['function'] ?? null,
                    'listeners' => $listenersData
                ]) : null;
        $model->fire_count = $fireCount;
        $model->save();
    }

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
        $model            = app(EventModel::class);
        $namespace        = get_class($this);
        $model->namespace = $namespace;

        return ($event = $model->where('namespace', $namespace)->first()) instanceof EventModel ? $event : $model;
    }

    /**
     * @param string $event
     * @return array
     */
    private function collectListenersData(string $event): array
    {
        $listenersData = [];
        $listeners     = app('events')->getListeners($event);

        if (!empty($listeners)) {
            foreach ($listeners as $listener) {
                if (!$listener instanceof \Closure) {
                    continue;
                }

                $reflection = new \ReflectionFunction($listener);
                $uses       = $reflection->getStaticVariables();

                if (!isset($uses['listener'])) {
                    continue;
                }

                $listenerParameter = $uses['listener'];

                if ($listenerParameter instanceof \Closure) {
                    $reflection = new \ReflectionFunction($listenerParameter);

                    $this->addListener([
                        'type'  => 'Closure',
                        'file'  => $reflection->getFileName(),
                        'lines' => sprintf('%s-%s', $reflection->getStartLine(), $reflection->getEndLine()),
                            ], $listenersData);
                } else if (is_string($listenerParameter)) {
                    if (strpos($listenerParameter, '@') === false) {
                        $namespace = $listenerParameter;
                    } else {
                        $parts     = explode('@', $listenerParameter);
                        $namespace = $parts[0];
                        $function  = $parts[1];
                    }

                    $this->addListener([
                        'type'      => 'Class',
                        'namespace' => $namespace,
                        'function'  => $function ?? 'handle'
                            ], $listenersData);
                }
            }
        }

        return $listenersData;
    }

    /**
     * @param array $listener
     * @param array $listeners
     */
    public function addListener(array $listener, array &$listeners)
    {
        $hasSameListener = false;

        foreach ($listeners as $listenerData) {
            if ($listener['type'] != $listenerData['type']) {
                continue;
            }

            $type = $listener['type'];

            if ($type == 'Closure' && $listener['file'] == $listenerData['file'] && $listener['lines'] == $listenerData['lines']) {
                $hasSameListener = true;
                break;
            } else if ($type == 'Class' && $listener['namespace'] == $listenerData['namespace'] && $listener['function'] == $listenerData['function']) {
                $hasSameListener = true;
                break;
            }
        }

        if ($hasSameListener) {
            return;
        }

        $listeners[] = $listener;
    }

}
