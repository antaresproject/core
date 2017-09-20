<?php
/**
 * Created by PhpStorm.
 * User: Marcin Kozak
 * Date: 2017-09-19
 * Time: 14:30
 */

namespace Antares\Form\Controls;

use Closure;

class CustomType extends AbstractType
{

    /**
     * @var Closure
     */
    protected $callback;

    /**
     * @var string
     */
    protected $type = 'custom';

    /**
     * CustomType constructor.
     * @param string $name
     * @param Closure $callback
     * @param array $attributes
     */
    public function __construct(string $name, Closure $callback, array $attributes = [])
    {
        parent::__construct($name, $attributes);

        $this->callback = $callback;
    }

    /**
     * Rendering this very control
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderControl()
    {
        return (string) call_user_func($this->callback, $this);
    }

}