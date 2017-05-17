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


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblLogsLoginDevicesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_logs_login_devices', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->index('user_id');
            $table->integer('log_id')->unsigned()->nullable()->index('log_id');
            $table->string('name');
            $table->string('ip_address', 50);
            $table->string('browser');
            $table->string('system');
            $table->string('machine')->nullable();
            $table->text('location')->nullable();
            $table->timestamps();
        });
        Schema::table('tbl_logs_login_devices', function(Blueprint $table) {
            $table->foreign('user_id', 'tbl_logs_login_devices_fk1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('log_id', 'tbl_logs_login_devices_fk2')->references('id')->on('tbl_logs')->onUpdate('NO ACTION')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_logs_login_devices');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
