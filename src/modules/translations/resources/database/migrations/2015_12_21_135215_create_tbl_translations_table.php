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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class CreateTblTranslationsTable extends Migration
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
            Schema::create('tbl_translations', function(Blueprint $table) {
                $table->increments('id', true);
                $table->string('locale');
                $table->text('area')->nullable();
                $table->integer('lang_id')->unsigned()->index('lang_id_2');
                $table->string('group');
                $table->string('key');
                $table->text('value', 65535)->nullable();
                $table->timestamps();
            });
            Schema::table('tbl_translations', function(Blueprint $table) {
                $table->foreign('lang_id', 'tbl_translations_ibfk_1')->references('id')->on('tbl_languages')->onUpdate('RESTRICT')->onDelete('CASCADE');
            });
        } catch (Exception $e) {
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
        Schema::dropIfExists('tbl_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
