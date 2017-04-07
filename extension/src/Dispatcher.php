<?php

declare(strict_types=1);

namespace Antares\Extension;

use Antares\Extension\Collections\Extensions;
use Antares\Extension\Contracts\ExtensionContract;
use Antares\Extension\Events\Booted;
use Antares\Extension\Events\BootedAll;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Closure;
use Exception;

class Dispatcher {

    /**
     * Container instance.
     *
     * @var Container
     */
    protected $container;

    /**
	 * Event dispatcher instance.
	 *
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * Loader instance.
     *
     * @var Loader
     */
    protected $loader;

    /**
     * Booted indicator.
     *
     * @var bool
     */
    protected $booted = false;

    /**
	 * List of extensions.
	 *
     * @var ExtensionContract[]
     */
    protected $extensions = [];

    /**
     * Dispatcher constructor.
     * @param Container $container
     * @param EventDispatcher $eventDispatcher
     * @param Loader $loader
     */
    public function __construct(Container $container, EventDispatcher $eventDispatcher, Loader $loader) {
        $this->container        = $container;
        $this->eventDispatcher  = $eventDispatcher;
        $this->loader           = $loader;
    }

    /**
     * Checks if booted.
     *
     * @return bool
     */
    public function booted() : bool {
        return $this->booted;
    }

    /**
     * @param Extensions $extensions
     */
    public function registerCollection(Extensions $extensions) {
        foreach ($extensions as $extension) {
            $this->register($extension);
        }
    }

    /**
     * Register the extension.
     *
     * @param  ExtensionContract $extension
     */
    public function register(ExtensionContract $extension) {
        $this->extensions[] = $extension;
    }

    /**
     * Boot all extensions.
     *
     * @throws Exception
     */
    public function boot() {
        foreach ($this->extensions as $extension) {
            try {
                $this->loader->register($extension);
                $this->eventDispatcher->dispatch(new Booted($extension));
            }
            catch(Exception $e) {
                throw $e;
            }
        }

        $this->eventDispatcher->dispatch(new BootedAll());
        $this->booted = true;
    }

    /**
     * Create an event listener or execute it directly.
     *
     * @param Closure|null $callback
     */
    public function after(Closure $callback = null) {
        if ($callback && $this->booted()) {
            $this->container->call($callback);
        }

        $this->eventDispatcher->listen(BootedAll::class, $callback);
    }

}
