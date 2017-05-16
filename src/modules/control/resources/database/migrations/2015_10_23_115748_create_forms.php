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
 * @package    Access Control
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForms extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_forms', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('component_id')->unsigned()->index('component_id');
            $table->integer('action_id')->unsigned()->index('action_id');
            $table->text('value', 65535)->nullable();
        });
        Schema::create('tbl_forms_config', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('form_id')->unsigned()->index('form_id');
            $table->integer('brand_id')->unsigned()->index('brand_id');
            $table->integer('role_id')->unsigned()->index('role_id');
            $table->text('value', 65535)->nullable();
        });
        Schema::table('tbl_forms', function(Blueprint $table) {
            $table->foreign('component_id', 'tbl_forms_ibfk_1')->references('id')->on('tbl_components')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('action_id', 'tbl_forms_ibfk_2')->references('id')->on('tbl_actions')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_forms_config', function(Blueprint $table) {
            $table->foreign('brand_id', 'tbl_forms_config_ibfk_2')->references('id')->on('tbl_brands')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('role_id', 'tbl_forms_config_ibfk_3')->references('id')->on('tbl_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('form_id', 'tbl_forms_config_ibfk_4')->references('id')->on('tbl_forms')->onUpdate('NO ACTION')->onDelete('CASCADE');
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
        if (Schema::hasTable('tbl_forms_config')) {
            Schema::table('tbl_forms_config', function(Blueprint $table) {
                $table->dropForeign('tbl_forms_config_ibfk_2');
                $table->dropForeign('tbl_forms_config_ibfk_3');
                $table->dropForeign('tbl_forms_config_ibfk_4');
            });
            Schema::drop('tbl_forms_config');
        }
        if (Schema::hasTable('tbl_forms')) {
            Schema::table('tbl_forms', function(Blueprint $table) {
                $table->dropForeign('tbl_forms_ibfk_1');
                $table->dropForeign('tbl_forms_ibfk_2');
            });

            Schema::drop('tbl_forms');
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
