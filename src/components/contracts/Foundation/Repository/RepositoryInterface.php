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
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */


namespace Antares\Contracts\Foundation\Repository;

interface RepositoryInterface
{

    /**
     * fetch all rows
     * 
     * @param array $columns
     */
    public function all($columns = array('*'));

    /**
     * create new model instance
     * 
     * @param array $data
     */
    public function create(array $data);

    /**
     * updates model instance
     * 
     * @param array $data
     * @param mixed $id
     */
    public function update(array $data, $id);

    /**
     * deletes row by identifier
     * 
     * @param mixed $id
     */
    public function delete($id);

    /**
     * find row by identifier
     * 
     * @param mixed $id
     * @param array $columns
     */
    public function find($id, $columns = array('*'));

    /**
     * find row by field
     * 
     * @param mixed $field
     * @param mixed $value
     * @param array $columns
     */
    public function findBy($field, $value, $columns = array('*'));
}
