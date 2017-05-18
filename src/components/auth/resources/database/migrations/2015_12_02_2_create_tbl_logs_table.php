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
use Illuminate\Support\Facades\Log;

class CreateTblLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            $this->down();
            Schema::create('tbl_log_priorities', function(Blueprint $table) {
                $table->increments('id');
                $table->smallInteger('num')->unsigned()->index('num');
                $table->string('name', 50);
            });
            Schema::create('tbl_log_types', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique('name');
                $table->boolean('active')->default(1);
                $table->timestamp('created_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            });

            Schema::create('tbl_logs', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('type_id')->unsigned()->nullable();
                $table->integer('brand_id')->unsigned()->nullable()->index('brand_id');
                $table->integer('user_id')->unsigned()->nullable()->index('user_id');
                $table->integer('client_id')->unsigned()->nullable()->index('client_id');
                $table->integer('author_id')->unsigned()->nullable()->index('author_id');
                $table->integer('priority_id')->unsigned()->nullable()->index('priority_id');
                $table->string('owner_type');
                $table->integer('owner_id');
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->text('related_data')->nullable();
                $table->text('additional_params')->nullable();
                $table->string('type');
                $table->string('name');
                $table->string('route');
                $table->string('ip_address', 64);
                $table->string('user_agent');
                $table->tinyInteger('is_api_request')->default(0);
                $table->timestamps();
                $table->index(['type_id', 'brand_id', 'user_id', 'client_id', 'priority_id'], 'type_id');
            });
            Schema::table('tbl_logs', function(Blueprint $table) {
                $table->foreign('priority_id', 'tbl_logs_ibfk_4')->references('id')->on('tbl_log_priorities')->onUpdate('NO ACTION')->onDelete('SET NULL');
                $table->foreign('type_id', 'tbl_logs_ibfk_1')->references('id')->on('tbl_log_types')->onUpdate('NO ACTION')->onDelete('SET NULL');

                $table->foreign('brand_id', 'tbl_logs_ibfk_2')
                        ->references('id')
                        ->on('tbl_brands')
                        ->onUpdate('NO ACTION')
                        ->onDelete('SET NULL');

                $table->foreign('user_id', 'tbl_logs_ibfk_3')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('SET NULL');
                $table->foreign('author_id', 'tbl_logs_author_id_fk')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });


            Schema::create('tbl_checksum', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->index('name');
                $table->text('value')->nullable();
            });
        } catch (\Exception $e) {
            Log::emergency($e);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_logs');
        Schema::dropIfExists('tbl_log_priorities');
        Schema::dropIfExists('tbl_log_types');
        Schema::dropIfExists('tbl_checksum');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
