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
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AntaresMemoryCreateSchemasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();
        $error = null;
        $this->down();
        try {
            $this->createComponentsTbl();
            $this->createActionCategoriesTbl();
            $this->createActionsTbl();
            $this->createRolesTbl();
            $this->createBrandsTbl();
            $this->createPermissionsTbl();
            $this->createOptionsTbl();
            $this->createPermissionsView();
            $this->createInstallationProgressTable();
        } catch (\Exception $ex) {
            Log::emergency($ex);
            $error = $ex;
        }
        if (!is_null($error)) {
            DB::rollback();
            throw new \Exception($error);
        } else {
            DB::commit();
        }
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_permissions');
        Schema::dropIfExists('tbl_roles');
        Schema::dropIfExists('tbl_actions');
        Schema::dropIfExists('tbl_action_categories');
        Schema::dropIfExists('tbl_components');
        Schema::dropIfExists('tbl_brands');
        Schema::dropIfExists('tbl_antares_options');
        Schema::dropIfExists('tbl_antares_installation');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * COMPONENTS TABLE
     */
    private function createBrandsTbl()
    {
        Schema::create('tbl_brands', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->text('name');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('default')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        $datetime = Carbon::now();

        DB::table('tbl_brands')->insert([
            'name'       => 'Antares',
            'status'     => 1,
            'default'    => 1,
            'created_at' => $datetime
        ]);
    }

    /**
     * COMPONENTS TABLE
     */
    private function createComponentsTbl()
    {
        Schema::create('tbl_components', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('vendor');
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->boolean('required')->default(0);
            $table->text('options')->nullable();

            $table->unique(['vendor', 'name']);
        });
    }

    /**
     * ACTIONS TABLE
     */
    private function createActionsTbl()
    {
        Schema::create('tbl_actions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('component_id')->unsigned();
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('name');
            $table->string('description', 500)->nullable();
        });
        Schema::table('tbl_actions', function (Blueprint $table) {
            $table->foreign('component_id')->references('id')->on('tbl_components')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('tbl_action_categories')->onDelete('cascade');
        });
    }

    /**
     * ACTIONS TABLE
     */
    private function createActionCategoriesTbl()
    {
        Schema::create('tbl_action_categories', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('description', 1000)->nullable();
        });
    }

    /**
     * ROLES TABLE
     */
    private function createRolesTbl()
    {
        Schema::dropIfExists('tbl_roles');
        Schema::create('tbl_roles', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('parent_id')->unsigned()->nullable()->index('parent_id_idx');
            $table->string('area')->nullable();
            $table->string('name');
            $table->string('full_name');
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('tbl_roles', function(Blueprint $table) {
            $table->foreign('parent_id')
                    ->references('id')
                    ->on('tbl_roles')
                    ->onDelete('SET NULL')
                    ->onUpdate('NO ACTION');
        });
    }

    /**
     * PERMISSIONS TABLE
     */
    private function createPermissionsTbl()
    {
        Schema::create('tbl_permissions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('brand_id')->unsigned();
            $table->integer('component_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('action_id')->unsigned();
            $table->tinyInteger('allowed')->default(1);
        });
        Schema::table('tbl_permissions', function(Blueprint $table) {
            $table->foreign('brand_id')
                    ->references('id')
                    ->on('tbl_brands')
                    ->onDelete('cascade');

            $table->foreign('component_id')
                    ->references('id')
                    ->on('tbl_components')
                    ->onDelete('cascade');

            $table->foreign('role_id')
                    ->references('id')
                    ->on('tbl_roles')
                    ->onDelete('cascade');

            $table->foreign('action_id')
                    ->references('id')
                    ->on('tbl_actions')
                    ->onDelete('cascade');
        });
    }

    /**
     * CREATE SCHEMAS BY FILE
     */
    public function createPermissionsView()
    {
        $schemasPath = __DIR__ . DIRECTORY_SEPARATOR . 'schemas';
        if (is_dir($schemasPath)) {
            $fileSystem = app('files');
            $files      = $fileSystem->allFiles($schemasPath);
            foreach ($files as $file) {
                DB::unprepared($file->getContents());
            }
        }
    }

    /**
     * CREATE SCHEMAS BY FILE
     */
    public function createOptionsTbl()
    {
        Schema::create('tbl_antares_options', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->nullable();
            $table->text('value')->nullable();
        });
    }

    /**
     * CREATE SCHEMAS FOR INSTALLATION PROGRESS
     */
    public function createInstallationProgressTable() {
        Schema::create('tbl_antares_installation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->text('content')->nullable();
        });
    }

}
