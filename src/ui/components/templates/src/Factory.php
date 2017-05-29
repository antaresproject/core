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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\UI\UIComponents;

use Antares\UI\UIComponents\Traits\DispatchableTrait;
use Antares\UI\UIComponents\Model\Components;
use Illuminate\Contracts\Container\Container;
use Antares\UI\UIComponents\Service\Service;
use Illuminate\Support\Collection;
use Antares\Memory\ContainerTrait;
use Illuminate\Http\Response;

class Factory
{

    use ContainerTrait,
        DispatchableTrait;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $app;

    /**
     * Response instance.
     *
     * @var Response
     */
    protected $response;

    /**
     * List of ui components.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $components;

    /**
     * List of ui component templates.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $templates;

    /**
     * Construct
     *
     * @param Container $app
     * @param Response $response
     */
    public function __construct(Container $app, Response $response)
    {
        $this->app        = $app;
        $this->response   = $response;
        $this->components = new Collection();
    }

    /**
     * Detect all ui components.
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function detect()
    {
        $this->app->make('events')->fire('antares.ui-components: detecting');

        $components = $this->finder()->detect();
        $service    = app(Service::class);
        foreach ($components as $component) {
            $result = $service->findOne($component, uri());
            if (!empty($result)) {
                continue;
            }
            $this->app->make('ui-components')->save(array_get($component, 'name'), $component);
        }
        return $components;
    }

    /**
     * Detects ui component templates
     * 
     * @return array
     */
    public function detectTemplates()
    {
        $this->app->make('events')->fire('antares.ui-components.templates: detecting');
        return $this->templateFinder()->detect()->toArray();
    }

    /**
     * Get ui components finder.
     *
     * @return \Antares\UI\UIComponents\Contracts\Finder
     */
    public function finder()
    {

        return $this->app->make('antares.ui-components.finder');
    }

    /**
     * Get ui components templates finder.
     *
     * @return \Antares\UI\UIComponents\Contracts\Finder
     */
    public function templateFinder()
    {
        return $this->app->make('antares.ui-components.templates.finder');
    }

    /**
     * get instance of ui components base model
     * 
     * @return \Antares\UI\UIComponents\Model\Widgets
     */
    public function model()
    {
        return new Components();
    }

}
