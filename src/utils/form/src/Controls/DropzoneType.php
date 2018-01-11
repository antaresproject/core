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

namespace Antares\Form\Controls;

class DropzoneType extends AbstractType
{

    /** @var string */
    protected $type = 'file';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string|null
     */
    protected $currentImagePath;

    /**
     * Is type of configurable?
     *
     * @var bool
     */
    protected $configurable = false;

    /**
     * Rendering this very control
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderControl()
    {
        $path = $this->configurable
            ? 'antares/foundation::form.controls.configured_dropzone'
            : 'antares/foundation::form.controls.dropzone';

        return view($path, [
            'control' => $this,
            'current_image_path' => $this->currentImagePath,
            'options' =>  (object) $this->options,
        ]);
    }

    /**
     * Sets the control as configurable.
     *
     * @param bool $state
     * @return $this
     */
    public function setAsConfigurable(bool $state = true) {
        $this->configurable = $state;

        return $this;
    }

    /**
     * @param string|null $path
     * @return $this
     */
    public function setCurrentImage(string $path = null) {
        $this->currentImagePath = $path;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    public function render()
    {
        app('antares.asset')->container('antares/foundation::application')->add('webpack_brand_settings', '/webpack/view_brand_settings.js', ['app_cache']);

        $this->addAttribute('class', 'dropzone dropzone-form brand-logo dz-clickable');
        $this->addAttribute('id', $this->name);

        return parent::render();
    }

}
