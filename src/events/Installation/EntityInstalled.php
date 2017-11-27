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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Events\Installation;

use Illuminate\Database\Eloquent\Model;
use Antares\Foundation\Events\AbstractEvent;

class EntityInstalled extends AbstractEvent
{

    /** @var string */
    protected static $name = 'Antares install: Entity installed';

    /** @var string */
    protected static $description = 'Runs after entity (e.g. user) installed';

    /** @var Model */
    public $entity;

    /** @var array */
    public $data;

    /** @var string */
    public $entityName;

    /**
     * EntityInstalled constructor
     *
     * @param string $entityName
     * @param Model  $entity
     * @param array  $data
     */
    public function __construct(string $entityName, Model $entity, array $data = [])
    {
        $this->entityName = $entityName;
        $this->entity = $entity;
        $this->data = $data;

        parent::__construct();
    }

}
