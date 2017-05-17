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
use Illuminate\Support\Facades\DB;

class CreateNotificationsTable extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $this->down();

        Schema::create('tbl_notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('severity_id')->unsigned()->nullable()->index('severity_id_idx');
            $table->integer('category_id')->unsigned()->nullable()->index('category_id_id_1');
            $table->integer('type_id')->unsigned()->nullable()->index('type_id_1');
            $table->boolean('active')->default(0);
            $table->string('event');
        });
        Schema::create('tbl_notification_categories', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique('name');
            $table->string('title');
        });
        Schema::create('tbl_notification_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique('name');
            $table->string('title');
        });
        Schema::create('tbl_notification_severity', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique('name');
        });
        Schema::create('tbl_notification_contents', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id')->unsigned()->nullable()->index('notification_id_1');
            $table->integer('lang_id')->unsigned()->index('notification_contents_lang_id');
            $table->string('title', 500);
            $table->text('content');
        });
        Schema::create('tbl_notifications_stack', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('notification_id')->unsigned()->nullable()->index('notification_stack_idx');
            $table->integer('author_id')->unsigned()->nullable()->index('notification_stack_author_idx');
            $table->text('variables')->nullable();
            $table->timestamps();
        });
        Schema::create('tbl_notifications_stack_params', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('stack_id')->unsigned()->nullable()->index('stack_params_stack_id_idx');
            $table->integer('model_id')->unsigned()->nullable()->index('stack_params_model_id_idx');
        });

        Schema::create('tbl_notifications_stack_read', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('stack_id')->unsigned()->nullable()->index('stack_id_idx');
            $table->integer('user_id')->unsigned()->nullable()->index('user_id_idx');
            $table->softDeletes();
        });
        Schema::table('tbl_notifications', function(Blueprint $table) {
            $table->foreign('severity_id', 'fk_severity_id')->references('id')->on('tbl_notification_severity')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('category_id', 'fk_not_category_id')->references('id')->on('tbl_notification_categories')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('type_id', 'fk_not_type_id')->references('id')->on('tbl_notification_types')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_notification_contents', function(Blueprint $table) {
            $table->foreign('notification_id', 'fk_notification_id')->references('id')->on('tbl_notifications')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('lang_id', 'fk_notification_contents_lang')->references('id')->on('tbl_languages')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_notifications_stack', function(Blueprint $table) {
            $table->foreign('notification_id', 'fk_notification_stack')->references('id')->on('tbl_notifications')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('author_id', 'fk_notification_stack_author_id')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_notifications_stack_params', function(Blueprint $table) {
            $table->foreign('stack_id', 'fk_notifications_stack_read_stack_id')->references('id')->on('tbl_notifications_stack')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_notifications_stack_read', function(Blueprint $table) {

            //$table->foreign('stack_id', 'fk_notifications_stack_read_stack_id')->references('id')->on('tbl_notifications_stack')->onUpdate('NO ACTION')->onDelete('CASCADE');

            $table->foreign('user_id', 'fk_notification_stack_read_user_id')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        $orderToClear = [
            'tbl_notifications_stack_read',
            'tbl_notifications_stack_params',
            'tbl_notifications_stack',
            'tbl_notification_contents',
            'tbl_notifications',
            'tbl_notification_categories',
            'tbl_notification_types',
            'tbl_notification_severity',
        ];

        foreach ($orderToClear as $tablename) {
            if (Schema::hasTable($tablename)) {
                DB::unprepared('DELETE FROM ' . $tablename);
            }
        }

        $orderToDelete = [
            'tbl_notifications_stack_read',
            'tbl_notifications_stack_params',
            'tbl_notifications_stack',
            'tbl_notification_severity',
            'tbl_notification_contents',
            'tbl_notifications',
            'tbl_notification_types',
            'tbl_notification_categories'
        ];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($orderToDelete as $tablename) {
            Schema::dropIfExists($tablename);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
