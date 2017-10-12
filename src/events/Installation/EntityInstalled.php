<?php

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
