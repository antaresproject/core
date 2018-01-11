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
 * @package    UI
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\Navigation\Breadcrumbs;

use Antares\UI\Navigation\Factory;
use Antares\UI\Navigation\Menu;
use Closure;
use Exception;

class Manager
{

    /**
     * @var CurrentRoute
     */
    protected $currentRoute;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @var array
     */
    protected $callbacks = [];

    /**
     * @var bool
     */
    protected $withMeta = true;

    /**
     * @var bool
     */
    protected $enabled = false;

    /**
     * @var bool
     */
    protected $isGenerated = false;

    /**
     * @var array|Menu[]
     */
    protected $generated = [];

    /**
     * Manager constructor.
     * @param CurrentRoute $currentRoute
     * @param Factory $factory
     * @param Generator $generator
     */
    public function __construct(CurrentRoute $currentRoute, Factory $factory, Generator $generator)
    {
        $this->currentRoute = $currentRoute;
        $this->factory      = $factory;
        $this->generator    = $generator;
    }

    /**
     * @param bool $state
     */
    public function enabled(bool $state): void
    {
        $this->enabled = $state;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool
    {
        return $this->isGenerated;
    }

    /**
     * @param bool $state
     */
    public function withMeta(bool $state): void
    {
        $this->withMeta = $state;
    }

    /**
     * @param string $name
     * @param Closure|string $callback
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function register(string $name, $callback): void
    {
        if (array_key_exists($name, $this->callbacks)) {
            throw new Exception("Breadcrumb name \"{$name}\" has already been registered.");
        }

        if ($callback instanceof Closure || is_string($callback)) {
            $this->callbacks[$name] = $callback;
        } else {
            throw new \InvalidArgumentException('The callback is invalid.');
        }
    }

    /**
     * @param string|null $name
     * @return bool
     */
    public function exists(string $name = null): bool
    {
        if ($name === null) {
            try {
                list($name) = $this->currentRoute->get();
            } catch (Exception $e) {
                return false;
            }
        }

        return isset($this->callbacks[$name]);
    }

    /**
     * @param string|null $name
     * @param array ...$params
     * @return array
     */
    public function generate(string $name = null, ...$params): array
    {
        if ($this->isGenerated) {
            return $this->generated;
        }
        if ($name === null) {
            list($name, $params) = $this->currentRoute->get();
        }


        $this->isGenerated = true;
        return $this->generated   = $this->generator->generate($this->callbacks, $name, $params);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        if (!$this->isGenerated) {
            $this->generate();
        }
        return $this->factory->breadcrumb()->render();
    }

    public function setupMeta(): void
    {
        if ($this->withMeta && $this->isGenerated) {
            $labels = [];

            foreach ($this->generated as $menu) {
                $labels[] = $menu->getMenuItem()->getLabel();
            };

            set_meta('title', implode(' | ', array_reverse($labels)));
        }
    }

    /**
     * @param string $name
     * @param array ...$params
     */
    public function setCurrentRoute(string $name, ...$params): void
    {
        $this->currentRoute->set($name, $params);
    }

    public function clearCurrentRoute(): void
    {
        $this->currentRoute->clear();
    }

}
