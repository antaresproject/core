<?php

declare(strict_types=1);

namespace Antares\Extension\Bootstrap;

use Antares\Extension\Dispatcher;
use Antares\Extension\Exception\ExtensionException;
use Antares\Extension\Manager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Exception;

class LoadExtension {

	/**
	 * Dispatcher instance.
	 *
	 * @var Dispatcher
	 */
	protected $dispatcher;

	/**
	 * Extension Manager instance.
	 *
	 * @var Manager
	 */
	protected $manager;

	/**
	 * LoadExtension constructor.
	 * @param Dispatcher $dispatcher
	 * @param Manager $manager
	 */
	public function __construct(Dispatcher $dispatcher, Manager $manager) {
		$this->dispatcher 	= $dispatcher;
		$this->manager 		= $manager;
	}

	/**
	 * Bootstrap the given application.
	 *
	 * @param Container $app
     * @throws ExtensionException
     * @throws FileNotFoundException
     * @throws Exception
	 */
	public function bootstrap(Container $app) {
	    $extensions = $this->manager->getAvailableExtensions()->filterByActivated();

        $this->dispatcher->registerCollection($extensions);
        $this->dispatcher->boot();
	}

}
