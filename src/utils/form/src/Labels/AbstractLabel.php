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
 * @package        Antares Core
 * @version        0.9.0
 * @author         Antares Team
 * @author         Mariusz Jucha <mariuszjucha@gmail.com>
 * @license        BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link           http://antaresproject.io
 */

namespace Antares\Form\Labels;

use Antares\Form\Contracts\Attributable;
use Antares\Form\Contracts\Wrapperable;
use Antares\Form\Controls\AbstractType;
use Antares\Form\Traits\AttributesTrait;
use Antares\Form\Traits\WrapperTrait;

/**
 * @author Mariusz Jucha <mariuszjucha@gmail.com>
 * Date: 24.03.17
 * Time: 11:16
 */
abstract class AbstractLabel implements Attributable
{

    use AttributesTrait;

    /** @var string */
    protected $name;

    /** @var string */
    protected $type;

    /** @var string */
    protected $info;

    /** @var AbstractType */
    protected $control;

    /**
     * AbstractLabel constructor
     *
     * @param string            $name
     * @param string            $info
     * @param AbstractType|null $control
     * @param array             $attributes
     */
    public function __construct(string $name, AbstractType $control = null, $info = '', array $attributes = [])
    {
        $this->name    = $name;
        $this->control = $control;
        $this->info    = $info;
        $this->setAttributes($attributes);
    }

    /**
     * @return bool
     */
    public function hasInfo()
    {
        return strlen($this->info) > 0;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function hasControl(): bool
    {
        return $this->control instanceof AbstractType;
    }

    /**
     * @param AbstractType $control
     */
    public function setControl(AbstractType $control)
    {
        $this->control = $control;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getInfo(): string
    {
        return $this->info;
    }

    /**
     * @param string $info
     */
    public function setInfo(string $info)
    {
        $this->info = $info;
    }

    public function render()
    {
        return view('antares/foundation::form.labels.' . $this->type, ['label' => $this, 'control' => $this->control])->render();
    }

}
