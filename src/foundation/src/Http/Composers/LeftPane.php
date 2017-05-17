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

namespace Antares\Foundation\Http\Composers;

use Illuminate\Container\Container;
use Illuminate\Routing\Router;
use Antares\UI\WidgetManager;
use Illuminate\View\View;

class LeftPane
{

    /**
     * widget default attributes
     * @var array
     */
    protected $defaultAttributes = [
        'name'       => 'default',
        'attributes' => [],
        'title'      => 'Default Pane',
        'view'       => 'antares/foundation::widgets.pane'
    ];

    /**
     * The widget manager implementation.
     * 
     * @var \Antares\UI\WidgetManager
     */
    protected $widget;

    /**
     * route parameters
     *
     * @var array 
     */
    protected $parameters = [];

    /**
     * Construct a new composer.
     * 
     * @param  WidgetManager  $widget
     * @param Router $router
     */
    public function __construct(WidgetManager $widget = null, Router $router)
    {
        $this->parameters = $router->current()->parameters();
        if (php_sapi_name() == 'cli') {
            return;
        }
        $this->widget = (is_null($widget)) ? Container::getInstance()->make('antares.widget') : $widget;
    }

    /**
     * Handle pane for left conatiner.
     * @return void
     */
    public function compose($name = null, $options = array())
    {
        if ($name instanceof View) {
            $name = null;
        }
        if (is_null($this->widget)) {
            return false;
        }

        $this->widget
                ->make((is_null($name) ? 'pane.left' : $name))
                ->add(isset($options['name']) ? $options['name'] : $this->defaultAttributes['name'])
                ->attributes(isset($options['attributes']) ? $options['attributes'] : $this->defaultAttributes['attributes'])
                ->title(trans(isset($options['title']) ? $options['title'] : $this->defaultAttributes['title']))
                ->content(view(isset($options['view']) ? $options['view'] : $this->defaultAttributes['view']));
    }

}
