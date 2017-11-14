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

        Schema::create('tbl_notification_types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('title');
        });

        Schema::create('tbl_notification_severity', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
        });

        Schema::create('tbl_notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('source')->index()->nullable();
            $table->string('category')->index()->default('system');
            $table->unsignedInteger('severity_id')->index();
            $table->unsignedInteger('type_id')->index();
            $table->string('event')->index()->nullable();
            $table->text('recipients')->nullable();
            $table->boolean('active')->default(1);
            $table->string('checksum', 500)->nullable();
            $table->timestamps();

            $table->foreign('severity_id')->references('id')->on('tbl_notification_severity')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('tbl_notification_types')->onDelete('cascade');
        });

        Schema::create('tbl_notification_contents', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notification_id')->index();
            $table->unsignedInteger('lang_id')->index();
            $table->string('title', 500)->nullable();
            $table->text('content');

            $table->foreign('notification_id')->references('id')->on('tbl_notifications')->onDelete('cascade');
            $table->foreign('lang_id')->references('id')->on('tbl_languages')->onDelete('cascade');
        });

        Schema::create('tbl_notifications_stack', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notification_id')->index();
            $table->unsignedInteger('author_id')->index();
            $table->text('variables')->nullable();
            $table->timestamps();

            $table->foreign('notification_id')->references('id')->on('tbl_notifications')->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('tbl_users')->onDelete('cascade');
        });

        Schema::create('tbl_notifications_stack_params', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stack_id')->index();
            $table->unsignedInteger('model_id')->nullable()->index('stack_params_model_id_idx');

            $table->foreign('stack_id')->references('id')->on('tbl_notifications_stack')->onDelete('cascade');
        });

        Schema::create('tbl_notifications_stack_read', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('stack_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->softDeletes();

            $table->foreign('stack_id')->references('id')->on('tbl_notifications_stack')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('tbl_users')->onDelete('cascade');
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
        ];
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($orderToDelete as $tablename) {
            Schema::dropIfExists($tablename);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
