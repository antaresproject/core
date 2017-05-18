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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AntaresAuthBasicRoles extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function() {
            $datetime = Carbon::now();
            DB::table('tbl_roles')->insert([
                'name'       => 'super-administrator',
                'full_name'  => 'Super Administrator',
                'area'       => 'admin',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ]);
            DB::table('tbl_roles')->insert([
                'parent_id'  => DB::getPdo()->lastInsertId(),
                'name'       => 'administrator',
                'area'       => 'admin',
                'full_name'  => 'Administrator',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ]);
            DB::table('tbl_roles')->insert([
                'parent_id'  => DB::getPdo()->lastInsertId(),
                'name'       => 'member',
                'area'       => 'users',
                'full_name'  => 'Member',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ]);
            DB::table('tbl_roles')->insert([
                'parent_id'  => DB::getPdo()->lastInsertId(),
                'name'       => 'guest',
                'full_name'  => 'Guest',
                'created_at' => $datetime,
                'updated_at' => $datetime,
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('tbl_roles')->delete();
    }

}
