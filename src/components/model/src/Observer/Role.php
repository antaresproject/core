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
 namespace Antares\Model\Observer;

use Antares\Model\Role as Eloquent;
use Antares\Contracts\Authorization\Factory;

class Role
{
    /**
     * The authorization factory implementation.
     *
     * @var \Antares\Contracts\Authorization\Factory
     */
    protected $acl;

    /**
     * Construct a new role observer.
     *
     * @param  \Antares\Contracts\Authorization\Factory  $acl
     */
    public function __construct(Factory $acl)
    {
        $this->acl = $acl;
    }

    /**
     * On creating observer.
     *
     * @param  \Antares\Model\Role  $model
     *
     * @return void
     */
    public function creating(Eloquent $model)
    {
        $this->acl->addRole($model->getAttribute('name'));
    }

    /**
     * On deleting observer.
     *
     * @param  \Antares\Model\Role  $model
     *
     * @return void
     */
    public function deleting(Eloquent $model)
    {
        $this->acl->removeRole($model->getAttribute('name'));
    }

    /**
     * On updating/restoring observer.
     *
     * @param  \Antares\Model\Role  $model
     *
     * @return void
     */
    public function updating(Eloquent $model)
    {
        $originalName = $model->getOriginal('name');
        $currentName  = $model->getAttribute('name');
        $deletedAt    = null;

        if ($model->isSoftDeleting()) {
            $deletedAt = $model->getDeletedAtColumn();
        }

        $isRestoring = function ($model, $deletedAt) {
            return (! is_null($deletedAt)
                && is_null($model->getAttribute($deletedAt))
                && ! is_null($model->getOriginal($deletedAt)));
        };

        if ($isRestoring($model, $deletedAt)) {
            $this->acl->addRole($currentName);
        } else {
            $this->acl->renameRole($originalName, $currentName);
        }
    }
}
