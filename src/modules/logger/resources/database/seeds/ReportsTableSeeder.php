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
 * @package    Logger
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */



use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{

    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run()
    {

        $this->down();
        DB::table('tbl_report_types')->insert([
            ['name' => 'analyzer'],
            ['name' => 'tester'],
        ]);
    }

    /**
     * delete all database occurences for report component
     */
    public function down()
    {
        DB::transaction(function() {
            DB::table('tbl_report_types')->delete();
        });
    }

}
