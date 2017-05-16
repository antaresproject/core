<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */






namespace Antares\Updater\Strategy\Adapter;

use stdClass;

class MigratorAdapter extends AbstractMigrator
{

    /**
     * down migration 
     * 
     * @param String $path
     * @param array $files
     * @return boolean|\Antares\Updater\Strategy\Adapter\MigratorAdapter
     */
    public function down($path, array $files = array())
    {
        $this->clearNotes();
        if (empty($files)) {
            $files = $this->supportMigrator->getMigrationFiles($path);
        }
        $this->delete($path, $files);
        foreach ($files as $file) {
            $this->note(sprintf('Down migration package %s', $file));
            $this->supportMigrator->resolve($file)->down();
        }
        return $this;
    }

    /**
     * delete from migration table
     * 
     * @param type $path
     * @param array $files
     * @return \Antares\Updater\Strategy\Adapter\MigratorAdapter
     */
    protected function delete($path, array $files = array())
    {
        $this->supportMigrator->requireFiles($path, $files);
        foreach ($files as $file) {
            $object            = new stdClass();
            $object->migration = $file;
            $this->supportMigrator->getRepository()->delete($object);
        }
        return $this;
    }

    /**
     * up migration
     * 
     * @param String $path
     * @param boolean $pretend
     * @return boolean|\Antares\Updater\Strategy\Adapter\MigratorAdapter
     */
    public function run($path, $pretend = false)
    {
        $this->clearNotes();
        $this->note('MIGRATOR: Prepare to up migration repository');
        $files = $this->supportMigrator->getMigrationFiles($path);
        if (empty($files)) {
            $this->note('MIGRATOR: Migration repository is empty. No migration files found in database resource directory.');
            return $this;
        }
        $this->delete($path, $files);
        $this->note('MIGRATOR: Run migration repository');

        $this->supportMigrator->run($path, []);
        $this->notes = array_merge($this->notes, $this->supportMigrator->getNotes());

        return $this;
    }

}
