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
 * @package    Updater
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class VersionTableSeeder extends Seeder
{

    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run()
    {

        $this->down();
        DB::table('tbl_version')->insert([
            ['id' => 1, 'description' => 'Default Version', 'changelog' => '', 'path' => '', 'db_version' => '0.9.0', 'app_version' => '0.9.0', 'last_update_date' => Carbon::now()->toDateTimeString(), 'is_actual' => 1],
        ]);
    }

    public function down()
    {
        DB::transaction(function() {
            DB::table('tbl_version')->delete();
        });
    }

}
