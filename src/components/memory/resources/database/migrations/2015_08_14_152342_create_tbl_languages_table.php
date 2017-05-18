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

class CreateTblLanguagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_languages', function(Blueprint $table) {
            $table->increments('id', true);
            $table->string('code', 2);
            $table->string('name', 100);
            $table->boolean('is_default')->default(0);
        });
        Schema::table('tbl_languages', function(Blueprint $table) {
            DB::table('tbl_languages')->insert([
                ['code' => 'en', 'name' => 'English', 'is_default' => 1]
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_languages');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
