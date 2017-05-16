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
 * @package    Automation
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class CreateJobsTable extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $this->down();
        DB::transaction(function() {

            Schema::create('tbl_jobs_category', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('title');
            });

            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue');
                $table->longText('payload');
                $table->tinyInteger('attempts')->unsigned();
                $table->tinyInteger('reserved')->unsigned();
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
                $table->index(['queue', 'reserved', 'reserved_at']);
            });
            Schema::create('tbl_jobs', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('component_id')->unsigned()->nullable()->index('component_id_1');
                $table->integer('category_id')->unsigned()->nullable()->index('catregory_id_1');
                $table->boolean('active')->default(1);
                $table->string('name')->unique('name');
                $table->text('value')->nullable();
                $table->timestamps();
                $table->index(['component_id'], 'component_id');
            });
            Schema::create('tbl_job_results', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('job_id')->unsigned()->nullable()->index('job_id_1');
                $table->boolean('has_error')->default(0);
                $table->double('runtime', 8, 2);
                $table->text('return')->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->index(['job_id'], 'job_id');
            });
            Schema::create('tbl_job_errors', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('result_job_id')->unsigned()->nullable()->index('result_job_id_id_1');
                $table->integer('code');
                $table->string('name');
                $table->text('return')->nullable();
                $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->index(['result_job_id'], 'result_job_id');
            });
        });
        Schema::table('tbl_jobs', function(Blueprint $table) {
            $table->foreign('component_id', 'fk_jobs_component_id')->references('id')->on('tbl_components')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('category_id', 'fk_jobs_category_id')->references('id')->on('tbl_jobs_category')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_job_results', function(Blueprint $table) {
            $table->foreign('job_id', 'fk_jobs_job_id')->references('id')->on('tbl_jobs')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_job_errors', function(Blueprint $table) {
            $table->foreign('result_job_id', 'fk_jobs_result_job_id')->references('id')->on('tbl_job_results')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('tbl_job_errors');
        Schema::dropIfExists('tbl_job_results');
        Schema::dropIfExists('tbl_jobs');
        Schema::dropIfExists('tbl_jobs_category');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
