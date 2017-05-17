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
 namespace Antares\Contracts\Html\Table;

use Antares\Contracts\Html\Grid as GridContract;

interface Grid extends GridContract
{
    /**
     * Attach Eloquent as row and allow pagination (if required).
     *
     * <code>
     *      // add model without pagination
     *      $table->with(User::all(), false);
     *
     *      // add model with pagination
     *      $table->with(User::paginate(30), true);
     * </code>
     *
     * @param  mixed  $model
     * @param  bool   $paginate
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function with($model, $paginate = true);

    /**
     * Attach rows data instead of assigning a model.
     *
     * <code>
     *      // assign a data
     *      $table->rows(DB::table('users')->get());
     * </code>
     *
     * @param  array  $rows
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function rows(array $rows = null);

    /**
     * Append a new column to the table.
     *
     * <code>
     *      // add a new column using just field name
     *      $table->column('username');
     *
     *      // add a new column using a label (header title) and field name
     *      $table->column('User Name', 'username');
     *
     *      // add a new column by using a field name and closure
     *      $table->column('fullname', function ($column)
     *      {
     *          $column->label = 'User Name';
     *          $column->value = function ($row) {
     *              return $row->first_name.' '.$row->last_name;
     *          };
     *
     *          $column->attributes(function ($row) {
     *              return array('data-id' => $row->id);
     *          });
     *      });
     * </code>
     *
     * @param  mixed  $name
     * @param  mixed|null  $callback
     *
     * @return \Antares\Contracts\Html\Table\Column
     */
    public function column($name, $callback = null);

    /**
     * Setup pagination.
     *
     * @param  int|null  $perPage
     *
     * @return $this
     */
    public function paginate($perPage);

    /**
     * Get whether current setup is paginated.
     *
     * @return bool
     */
    public function paginated();

    /**
     * Execute searchable filter on model instance.
     *
     * @param  array   $attributes
     * @param  string  $key
     *
     * @return void
     */
    public function searchable(array $attributes, $key = 'q');

    /**
     * Execute sortable query filter on model instance.
     *
     * @param  string  $orderByKey
     * @param  string  $directionKey
     *
     * @return void
     */
    public function sortable($orderByKey = 'order_by', $directionKey = 'direction');
}
