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


namespace Antares\Foundation\Http\Presenters;

use Antares\Contracts\Html\Form\Presenter as PresenterContract;
use Antares\Contracts\Html\Form\Factory;
use Antares\Contracts\Html\Form\Grid;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Antares\Support\Collection;
use function app;
use function handles;

abstract class Presenter implements PresenterContract
{

    /**
     * Implementation of form contract.
     *
     * @var Factory
     */
    protected $form;

    /**
     * table actions container
     *
     * @var array 
     */
    protected $tableActions = [];

    /**
     * add datatables action button
     * 
     * @param String $action
     * @param mixed $btn
     * @return Presenter
     */
    protected function addTableAction($action, $row, $btn)
    {
        if (empty($this->tableActions)) {
            $this->tableActions = new Collection();
        }
        $path = Route::getCurrentRoute()->getPath();
        Event::fire('datatables:' . $path . ':before.action.' . $action, [$this->tableActions, $row]);
        $this->tableActions->push($btn);
        Event::fire('datatables:' . $path . ':after.action.' . $action, [$this->tableActions, $row]);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function handles($url)
    {
        return handles($url);
    }

    /**
     * {@inheritdoc}
     */
    public function setupForm(Grid $form)
    {
        $form->layout('antares/foundation::components.form');
    }

    /**
     * scripts containers resolver
     * 
     * @return array
     */
    private function getScriptsContainers()
    {
        $container = null;
        $config    = null;
        foreach (['container', 'app', 'foundation'] as $name) {
            if (isset($this->{$name}) && is_null($container)) {
                $container = $this->{$name}->make('antares.asset');
                $config    = $this->{$name}->make('config');
                break;
            }
        }
        if (is_null($container)) {
            $container = app('antares.asset');
        }
        if (is_null($config)) {
            $config = app('config');
        }
        return [
            'container' => $container,
            'config'    => $config
        ];
    }

    /**
     * create presenter additional scripts
     * 
     * @param String $key
     * @param String $position
     * @return void
     */
    protected function scripts($key = null, $position = 'antares/foundation::scripts')
    {

        if (!$key) {
            return false;
        }
        $containers = $this->getScriptsContainers();
        $config     = $containers['config']->get($key);
        $container  = $containers['container']->container(isset($config['position']) ? $config['position'] : $position);
        if (isset($config['resources']) && !empty($config['resources'])) {
            foreach ($config['resources'] as $name => $path) {
                $container->script($name, $path);
            }
        }
        return;
    }

    /**
     * row actions decorator
     * 
     * @return String
     */
    public function decorateActions()
    {
        if (empty($this->tableActions)) {
            return '';
        }
        $html    = app('html');
        $section = $html->create('div', $html->raw(implode('', $this->tableActions->toArray())), ['class' => 'mass-actions-menu'])->get();
        return '<i class="zmdi zmdi-more"></i>' . app('html')->raw($section)->get();
    }

}
