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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblReportsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_report_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique('name');
            $table->boolean('active')->default(1);
            $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::create('tbl_reports', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('brand_id')->unsigned()->nullable()->index('brand_id_2');
            $table->integer('user_id')->unsigned()->nullable()->index('user_id_2');
            $table->integer('type_id')->unsigned()->nullable()->index('type_id_2');
            $table->string('name')->unique('name');
            $table->binary('html');
            $table->timestamps();
            $table->index(['type_id', 'brand_id', 'user_id'], 'type_id');
        });
        Schema::table('tbl_reports', function(Blueprint $table) {
            $table->foreign('brand_id', 'fk_brand_id')->references('id')->on('tbl_brands')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign('type_id', 'fk_type_id')->references('id')->on('tbl_report_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'fk_user_id')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
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
        Schema::dropIfExists('tbl_reports');
        Schema::dropIfExists('tbl_report_types');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
