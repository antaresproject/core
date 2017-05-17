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


use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AntaresAuthCreateUsersActivityTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_users_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->length(10)->nullable()->unsigned()->index('tbl_users_activity_user_id');
            $table->dateTime('last_activity');
            $table->timestamps();
        });

        Schema::table('tbl_users_activity', function (Blueprint $table) {
            $table->foreign('user_id', 'tbl_users_activity_user_id_ibfk_1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if (Schema::hasTable('tbl_users_activity')) {
            Schema::drop('tbl_users_activity');
        }

        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
