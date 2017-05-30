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


namespace Antares\Html\Control;

use Antares\Contracts\Html\Form\Field as FieldContract;
use Antares\Asset\Factory as AssetFactory;

class Control
{

    /**
     * Asset factory.
     *
     * @var \Antares\Asset\Factory
     */
    protected $asset;

    /**
     * params container
     *
     * @var array 
     */
    protected $params = [];

    /**
     * field instance
     *
     * @var FieldContract
     */
    protected $field = null;

    /**
     * constructing
     * 
     * @param AssetFactory $assetFactory
     */
    public function __construct(AssetFactory $assetFactory)
    {
        $this->asset = $assetFactory;
    }

    /**
     * get script container
     * 
     * @return \Antares\Asset\Asset
     */
    protected function container()
    {
        return $this->asset->container('antares/foundation::scripts');
    }

    /**
     * params setter
     * 
     * @param array $params
     * @return \Antares\Html\Control\Control
     */
    public function setParams(array $params = [])
    {
        $this->params = $params;
        return $this;
    }

    /**
     * field setter
     * 
     * @param FieldContract $field
     * @return \Antares\Html\Control\Control
     */
    public function setField(FieldContract $field)
    {
        $this->setParams($field->getAttributes());
        $this->field = $field;
        return $this;
    }

}
