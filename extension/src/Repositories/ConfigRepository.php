<?php

declare(strict_types=1);

namespace Antares\Extension\Repositories;

class ConfigRepository {

    /**
     * Paths where extensions can be found.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * Reserved names of extensions.
     *
     * @var array
     */
    protected $reserved = [];

    /**
     * Path to the application root.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * Path to the public.
     *
     * @var string
     */
    protected $publicPath;

    /**
     * ConfigRepository constructor.
     * @param array $config
     * @param string $rootPath
     * @param string $publicPath
     */
    public function __construct(array $config, string $rootPath, string $publicPath) {
        $this->paths        = (array) array_get($config, 'paths', []);
        $this->reserved     = (array) array_get($config, 'reserved', []);
        $this->rootPath     = rtrim($rootPath, '/');
        $this->publicPath   = rtrim($publicPath, '/');
    }

    /**
     * Returns paths where extensions can be found.
     *
     * @return string[]
     */
    public function getPaths() : array {
        return $this->paths;
    }

    /**
     * Returns reserved names of extensions.
     *
     * @return string[]
     */
    public function getReservedNames() : array {
        return $this->reserved;
    }

    /**
     * Returns the system root path.
     *
     * @return string
     */
    public function getRootPath() : string {
        return $this->rootPath;
    }

    /**
     * Returns the public path.
     *
     * @return string
     */
    public function getPublicPath() : string {
        return $this->publicPath;
    }

}