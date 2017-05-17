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
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ComponentsTableSeeder extends Seeder
{

    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run()
    {
        $this->down();
        DB::transaction(function() {

            DB::table('tbl_widget_types')->insert([
                'id'          => 1,
                'slug'        => 'default',
                'name'        => 'Default Widget',
                'description' => 'Default widget type',
            ]);
            $typeId = DB::getPdo()->lastInsertId();
            DB::table('tbl_widgets')->insert([
                'type_id'    => $typeId,
                'name'       => 'Content',
                'created_at' => Carbon::now(),
            ]);
        });
        $schemasPath     = __DIR__ . DIRECTORY_SEPARATOR . '../schemas';
        $viewsSchemaPath = $schemasPath . DIRECTORY_SEPARATOR . 'default_widgets.sql';
        if (file_exists($viewsSchemaPath)) {
            DB::unprepared(file_get_contents($viewsSchemaPath));
        }
    }

    public function down()
    {
        DB::transaction(function() {
            if (Schema::hasTable('tbl_widget_types')) {
                DB::unprepared('DELETE FROM tbl_widget_types');
            }
            if (Schema::hasTable('tbl_widgets_params')) {
                DB::unprepared('DELETE FROM tbl_widgets_params');
            }
            if (Schema::hasTable('tbl_widgets')) {
                DB::unprepared('DELETE FROM tbl_widgets');
            }
        });
    }

}
