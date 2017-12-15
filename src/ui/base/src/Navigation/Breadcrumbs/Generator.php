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
 * @author     Original Orchestral https://github.com/orchestral
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\Navigation\Breadcrumbs;

use Antares\Authorization\Factory as AuthFactory;
use Antares\UI\Navigation\Factory;
use Antares\UI\Navigation\Menu;
use Illuminate\Contracts\Support\Arrayable;
use Exception;
use Illuminate\Routing\UrlGenerator;
use App;

class Generator implements Arrayable
{

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @var AuthFactory
     */
    protected $authFactory;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var array
     */
    protected $breadcrumbs = [];

    /**
     * @var array
     */
    protected $callbacks = [];

    /**
     * Generator constructor.
     * @param Factory $factory
     * @param AuthFactory $authFactory
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(Factory $factory, AuthFactory $authFactory, UrlGenerator $urlGenerator)
    {
        $this->factory      = $factory;
        $this->authFactory  = $authFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return AuthFactory
     */
    public function acl(): AuthFactory
    {
        return $this->authFactory;
    }

    /**
     * @return UrlGenerator
     */
    public function url(): UrlGenerator
    {
        return $this->urlGenerator;
    }

    /**
     * @param array $callbacks
     * @param string $name
     * @param array $params
     * @return array|Menu[]
     */
    public function generate(array $callbacks, string $name, array $params): array
    {
        $this->breadcrumbs = [];
        $this->callbacks   = $callbacks;

        $this->call($name, $params);

        return $this->toArray();
    }

    /**
     * @param string $name
     * @param array $params
     * @throws Exception
     */
    protected function call(string $name, array $params): void
    {
        if (!isset($this->callbacks[$name])) {
            return;
            //throw new Exception("Breadcrumb not found with name \"{$name}\".");
        }

        array_unshift($params, $this);

        $callback = $this->callbacks[$name];

        if (is_string($callback)) {
            $class = App::make($callback);

            if (method_exists($class, 'handle')) {
                $callback = [$class, 'handle'];
            }
        }

        call_user_func_array($callback, $params);
    }

    /**
     * @param string $name
     * @param array ...$params
     */
    public function parent(string $name, ...$params):  void
    {
        $this->call($name, $params);
    }

    /**
     * @param string $id
     * @param string $title
     * @param string|null $url
     * @param string|null $icon
     * @param array $data
     * @return Menu
     */
    public function push(string $id, string $title = null, string $url = null, string $icon = null, array $data = []): Menu
    {
        if ($title === null) {
            $title = $id;
        }

        $item = $this->factory->breadcrumb()->addItem($id, $title, $url, $icon, $data);

        $this->breadcrumbs[] = $item->getChild($id);

        return $item;
    }

    /**
     * @return array|Menu[]
     */
    public function toArray(): array
    {
        return $this->breadcrumbs;
    }

}
