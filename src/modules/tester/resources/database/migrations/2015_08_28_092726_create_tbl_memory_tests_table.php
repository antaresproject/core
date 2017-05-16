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
 * @package    Tester
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTblMemoryTestsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::transaction(function() {
            $this->down();
            Schema::create('tbl_memory_tests', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('component_id')->unsigned()->index('component_id');
                $table->string('name');
                $table->text('value')->nullable();
            });
            Schema::table('tbl_memory_tests', function(Blueprint $table) {
                $table->foreign('component_id', 'tbl_memory_tests_ibfk_1')
                        ->references('id')
                        ->on('tbl_components')
                        ->onUpdate('NO ACTION')
                        ->onDelete('CASCADE');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tbl_memory_tests')) {
            Schema::table('tbl_memory_tests', function(Blueprint $table) {
                $table->dropForeign('tbl_memory_tests_ibfk_1');
            });
            Schema::drop('tbl_memory_tests');
        }
    }

}
