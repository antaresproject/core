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
 * @package    UI\UIComponents
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComponents extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_widgets', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->unsigned()->index('type_id');
            $table->string('name')->index('name');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('tbl_widget_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('slug')->index('slug');
            $table->string('name')->index('name');
            $table->string('description', 500)->nullable();
        });
        Schema::create('tbl_widgets_params', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->index('parent_id');
            $table->integer('wid')->unsigned()->index('wid');
            $table->integer('uid')->unsigned()->index('uid');
            $table->integer('brand_id')->unsigned()->index('FK_tbl_widgets_params');
            $table->string('resource', 500)->nullable();
            $table->string('name')->index('tbl_widgets_custom_name_unique');
            $table->text('data')->nullable();
        });
        Schema::create('tbl_page_closure', function(Blueprint $table) {
            $table->increments('closure_id');
            $table->integer('ancestor')->unsigned()->index('page_closure_ancestor_foreign');
            $table->integer('descendant')->unsigned()->index('page_closure_descendant_foreign');
            $table->integer('depth')->unsigned();
        });

        Schema::table('tbl_widgets', function(Blueprint $table) {
            $table->foreign('type_id', 'tbl_widgets_ibfk_1')->references('id')->on('tbl_widget_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_widgets_params', function(Blueprint $table) {
            $table->foreign('brand_id', 'tbl_widgets_params_ibfk_4')->references('id')->on('tbl_brands')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('parent_id', 'tbl_widgets_params_ibfk_1')->references('id')->on('tbl_widgets_params')->onUpdate('NO ACTION')->onDelete('SET NULL');
            $table->foreign('wid', 'tbl_widgets_params_ibfk_2')->references('id')->on('tbl_widgets')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('uid', 'tbl_widgets_params_ibfk_3')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_page_closure', function(Blueprint $table) {
            $table->foreign('ancestor', 'page_closure_ancestor_foreign')->references('id')->on('tbl_widgets_params')->onUpdate('RESTRICT')->onDelete('CASCADE');
            $table->foreign('descendant', 'page_closure_descendant_foreign')->references('id')->on('tbl_widgets_params')->onUpdate('RESTRICT')->onDelete('CASCADE');
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
        Schema::dropIfExists('tbl_widgets');
        Schema::dropIfExists('tbl_widget_types');
        Schema::dropIfExists('tbl_page_closure');
        Schema::dropIfExists('tbl_widgets_params');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
