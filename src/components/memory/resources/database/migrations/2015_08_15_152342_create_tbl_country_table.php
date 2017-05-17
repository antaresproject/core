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

class CreateTblCountryTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        /**
         * Countries and regions from repository: https://github.com/marsminds/countries-regions-cities
         */

        Schema::create('tbl_country', function(Blueprint $table) {
            $table->increments('id');
            $table->char('code', 2);
            $table->string('name', 255);

            $table->unique('code');
            $table->unique('name');
        });

        Schema::create('tbl_regions', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id')->unsigned()->nullable();
            $table->string('name', 255);

            $table->index('country_id');
        });

        Schema::table('tbl_country', function(Blueprint $table) {
            $countriesSchemaPath = __DIR__ . '/seeds/countries.sql';
            if (!file_exists($countriesSchemaPath)) {
                throw new \Exception('Countries seed sql schema not exists.');
            }
            DB::unprepared(file_get_contents($countriesSchemaPath));
        });

        Schema::table('tbl_regions', function(Blueprint $table) {
            $regionsSchemaPath = __DIR__ . '/seeds/regions.sql';
            if (!file_exists($regionsSchemaPath)) {
                throw new \Exception('Regions seed sql schema not exists.');
            }
            DB::unprepared(file_get_contents($regionsSchemaPath));
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
        Schema::dropIfExists('tbl_regions');
        Schema::dropIfExists('tbl_country');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
