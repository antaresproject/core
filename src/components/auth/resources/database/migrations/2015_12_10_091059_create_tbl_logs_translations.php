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

class CreateTblLogsTranslations extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_logs_translations', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('lang_id')->unsigned()->nullable()->index('lang_id_idx');
            $table->integer('log_id')->unsigned()->nullable()->index('log_id_idx');
            $table->text('raw')->nullable();
            $table->text('text')->nullable();
        });
        Schema::table('tbl_logs_translations', function(Blueprint $table) {
            $table->foreign('lang_id', 'fk_tlt_lang_id')->references('id')->on('tbl_languages')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('log_id', 'fk_tlt_log_id')->references('id')->on('tbl_logs')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        Schema::dropIfExists('tbl_logs_translations');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
