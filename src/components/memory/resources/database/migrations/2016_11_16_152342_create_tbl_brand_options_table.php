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
use Antares\Translations\Models\Languages;
use Illuminate\Database\Schema\Blueprint;
use Antares\Brands\Model\DateFormat;
use Antares\Brands\Model\Country;
use Antares\Brands\Model\Brands;

class CreateTblBrandOptionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_brand_options', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('brand_id')->unsigned()->index('brand_id');
            $table->integer('country_id')->unsigned()->index('country_id');
            $table->integer('language_id')->unsigned()->index('language_id');
            $table->integer('date_format_id')->unsigned()->index('date_format_id');
            $table->boolean('maintenance');
            $table->string('url', 255)->nullable();
            $table->longText('header')->nullable();
            $table->longText('styles')->nullable();
            $table->longText('footer')->nullable();
        });

        Schema::create('tbl_brand_templates', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('brand_id')->unsigned()->index('templates_brand_id');
            $table->text('area')->nullable();
            $table->string('logo', 255);
            $table->string('favicon', 255);
            $table->string('composition', 255)->nullable();
            $table->string('styleset', 255)->nullable();
            $table->text('colors')->nullable();
        });
        Schema::table('tbl_brand_options', function(Blueprint $table) {
            $table->foreign('brand_id', 'tbl_brand_options_ibfk_1')->references('id')->on('tbl_brands')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('country_id', 'tbl_brand_options_ibfk_2')->references('id')->on('tbl_country')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('language_id', 'tbl_brand_options_ibfk_3')->references('id')->on('tbl_languages')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_brand_templates', function(Blueprint $table) {
            $table->foreign('brand_id', 'tbl_brand_templates_fk_1')->references('id')->on('tbl_brands')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        $this->setDefaultOptions();
    }

    /**
     * Sets brand default options
     * 
     * @return void
     */
    protected function setDefaultOptions()
    {
        $areas         = array_keys(config('areas.areas'));
        $default       = config('areas.default');
        $configuration = require_once(__DIR__ . '/../../../../../modules/brands/resources/config/install.php');

        $countryId    = Country::where('code', 'pl')->first()->id;
        $languageId   = Languages::where('code', 'en')->first()->id;
        $dateFormatId = DateFormat::first()->id;
        $brandId      = Brands::where('default', 1)->first()->id;
        $email        = require_once(__DIR__ . '/../../../../../modules/brands/resources/config/config.php');

        $defaults = [
            'brand_id'       => $brandId,
            'country_id'     => $countryId,
            'language_id'    => $languageId,
            'date_format_id' => $dateFormatId,
            'maintenance'    => 0,
            'url'            => url('/'),
            'header'         => array_get($email, 'default.header')->render(),
            'styles'         => array_get($email, 'default.styles')->render(),
            'footer'         => array_get($email, 'default.footer')->render()
        ];


        Schema::table('tbl_brand_options', function(Blueprint $table) use($defaults) {
            DB::table('tbl_brand_options')->insert($defaults);
        });

        foreach ($areas as $area) {
            $keyname = ($area != $default) ? 'optional' : 'default';
            Schema::table('tbl_brand_templates', function(Blueprint $table) use($configuration, $keyname, $area, $brandId) {
                $config = array_get($configuration, $keyname);
                DB::table('tbl_brand_templates')->insert([
                    'brand_id'    => $brandId,
                    'area'        => $area,
                    'composition' => array_get($config, 'composition'),
                    'styleset'    => array_get($config, 'styleset'),
                    'logo'        => array_get($config, 'logo'),
                    'favicon'     => array_get($config, 'favicon'),
                    'colors'      => serialize(array_get($config, 'colors')),
                ]);
            });
        }
        return;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if (Schema::hasTable('tbl_brand_options')) {
            DB::table('tbl_brand_options')->delete();
        }
        Schema::dropIfExists('tbl_brand_options');
        if (Schema::hasTable('tbl_brand_templates')) {
            DB::table('tbl_brand_templates')->delete();
        }
        Schema::dropIfExists('tbl_brand_templates');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
