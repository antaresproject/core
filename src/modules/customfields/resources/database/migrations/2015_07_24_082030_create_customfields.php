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
 * @package    Customfields
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomfields extends Migration
{

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $this->down();
        DB::transaction(function() {
            Schema::create('tbl_fields', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('brand_id')->unsigned()->index('brand_id');
                $table->integer('group_id')->unsigned()->index('group_id');
                $table->integer('type_id')->unsigned()->index('type_id');
                $table->tinyInteger('imported')->unsigned()->default(0);
                $table->string('name');
                $table->string('label')->nullable();
                $table->string('placeholder')->nullable();
                $table->string('value')->nullable();
                $table->string('description')->nullable();
                $table->tinyInteger('force_display')->unsigned()->default(0);
                $table->string('additional_attributes')->nullable();
            });
            Schema::create('tbl_fields_groups', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('category_id')->unsigned()->index('tbl_custom_fields_groups_category_id_foreign');
                $table->string('name');
            });
            Schema::create('tbl_fields_types_options', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('field_id')->unsigned()->index('field_id');
                $table->string('label')->nullable();
                $table->string('value');
            });
            Schema::create('tbl_fields_data', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id')->unsigned()->index('user_id');
                $table->string('namespace', 500)->nullable();
                $table->string('field_class', 500)->nullable();
                $table->integer('foreign_id')->nullable();
                $table->integer('field_id')->unsigned()->index('field_id');
                $table->integer('option_id')->unsigned()->nullable()->index('option_id');
                $table->text('data', 65535)->nullable();
            });

            Schema::create('tbl_fields_categories', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->unique('tbl_custom_fields_categories_name_unique');
                $table->text('description', 65535)->nullable();
            });
            Schema::create('tbl_fields_types', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('type')->nullable()->unique('tbl_custom_fields_types_name_unique');
                $table->boolean('multi')->default(0);
            });
            Schema::create('tbl_fields_types_validators', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('type_id')->unsigned();
                $table->integer('validator_id')->unsigned()->index('validator_id');
                $table->index(['type_id', 'validator_id'], 'type_id');
            });
            Schema::create('tbl_fields_validators_config', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('field_id')->unsigned();
                $table->integer('validator_id')->unsigned()->index('validator_id');
                $table->string('value')->nullable();
                $table->index(['field_id', 'validator_id'], 'field_id');
            });

            Schema::create('tbl_fields_validators', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('description', 500)->nullable();
                $table->boolean('customizable')->default(0);
                $table->string('default')->nullable();
            });
            Schema::create('tbl_fieldsets', function(Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
            });
            Schema::create('tbl_field_fieldsets', function(Blueprint $table) {
                $table->increments('id');
                $table->integer('field_id')->unsigned();
                $table->integer('fieldset_id')->unsigned();
            });
            Schema::table('tbl_fields_types_options', function(Blueprint $table) {
                $table->foreign('field_id', 'tbl_fields_types_options_ibfk_1')->references('id')->on('tbl_fields')->onUpdate('NO ACTION')->onDelete('CASCADE');
            });


            Schema::table('tbl_fields_types_validators', function(Blueprint $table) {
                $table->foreign('validator_id', 'tbl_fields_types_validators_ibfk_2')
                        ->references('id')
                        ->on('tbl_fields_validators')
                        ->onUpdate('NO ACTION')
                        ->onDelete('CASCADE');
                $table->foreign('type_id', 'tbl_fields_types_validators_ibfk_1')
                        ->references('id')
                        ->on('tbl_fields_types')
                        ->onUpdate('NO ACTION')
                        ->onDelete('CASCADE');
            });

            Schema::table('tbl_fields_validators_config', function(Blueprint $table) {
                $table->foreign('validator_id', 'tbl_fields_validators_config_ibfk_2')
                        ->references('id')
                        ->on('tbl_fields_validators')
                        ->onUpdate('NO ACTION')
                        ->onDelete('CASCADE');
                $table->foreign('field_id', 'tbl_fields_validators_config_ibfk_1')
                        ->references('id')
                        ->on('tbl_fields')
                        ->onUpdate('NO ACTION')
                        ->onDelete('CASCADE');
            });
            Schema::table('tbl_fields', function(Blueprint $table) {
                $table->foreign('group_id', 'tbl_fields_ibfk_1')
                        ->references('id')
                        ->on('tbl_fields_groups')
                        ->onUpdate('RESTRICT')
                        ->onDelete('RESTRICT');
                $table->foreign('type_id', 'tbl_fields_ibfk_2')
                        ->references('id')
                        ->on('tbl_fields_types')
                        ->onUpdate('RESTRICT')
                        ->onDelete('RESTRICT');
                $table->foreign('brand_id', 'tbl_fields_ibfk_3')
                        ->references('id')
                        ->on('tbl_brands')
                        ->onUpdate('CASCADE')
                        ->onDelete('NO ACTION');
                Schema::table('tbl_fields_data', function(Blueprint $table) {
                    $table->foreign('option_id', 'tbl_fields_data_ibfk_3')->references('id')->on('tbl_fields_types_options')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign('user_id', 'tbl_fields_data_ibfk_1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
                    $table->foreign('field_id', 'tbl_fields_data_ibfk_2')->references('id')->on('tbl_fields')->onUpdate('NO ACTION')->onDelete('CASCADE');
                });
            });

            $schemasPath     = __DIR__ . DIRECTORY_SEPARATOR . '../schemas';
            $viewsSchemaPath = $schemasPath . DIRECTORY_SEPARATOR . 'customfields.views.sql';
            if (!file_exists($viewsSchemaPath)) {
                throw new \Exception('Views component schema not exists.');
            }
            DB::unprepared(file_get_contents($viewsSchemaPath));
        });
        Schema::table('tbl_field_fieldsets', function(Blueprint $table) {
            $table->foreign('field_id', 'tbl_field_fieldsets_ibfk_1')
                    ->references('id')
                    ->on('tbl_fields')
                    ->onUpdate('NO ACTION')
                    ->onDelete('CASCADE');

            $table->foreign('fieldset_id', 'tbl_field_fieldsets_ibfk_2')
                    ->references('id')
                    ->on('tbl_fieldsets')
                    ->onUpdate('NO ACTION')
                    ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_field_fieldsets');
        Schema::dropIfExists('tbl_fieldsets');
        Schema::dropIfExists('tbl_fields_data');
        Schema::dropIfExists('tbl_fields_validators_config');
        Schema::dropIfExists('tbl_fields_types_validators');
        Schema::dropIfExists('tbl_fields_validator_custom');
        Schema::dropIfExists('tbl_fields_validators');
        Schema::dropIfExists('tbl_fields_types_options');
        Schema::dropIfExists('tbl_fields');
        Schema::dropIfExists('tbl_fields_types');
        Schema::dropIfExists('tbl_fields_groups');
        Schema::dropIfExists('tbl_fields_categories');
        Schema::dropIfExists('tbl_fields_types_validators');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
