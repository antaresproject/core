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


use Illuminate\Database\Seeder;

class NotificationTypesSeeder extends Seeder
{

    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run()
    {
        $this->down();
        $types = [
                ['name' => 'email', 'title' => 'Email'],
                ['name' => 'sms', 'title' => 'Sms']
        ];
        $areas = config('areas.areas');
        foreach ($areas as $key => $value) {
            $types[] = ['name' => $key, 'title' => $value];
        }
        $types[] = ['name' => config('antares/foundation::handles'), 'title' => config('antares/foundation::application.name')];
        DB::table('tbl_notification_types')->insert($types);
        DB::table('tbl_notification_categories')->insert([
                ['name' => 'default', 'title' => 'System'],
                ['name' => 'users', 'title' => 'Users']
        ]);
    }

    /**
     * delete all database occurences for logger component
     */
    public function down()
    {
        if (Schema::hasTable('tbl_notification_types')) {
            DB::table('tbl_notification_types')->delete();
        }
        if (Schema::hasTable('tbl_notification_categories')) {
            DB::table('tbl_notification_categories')->delete();
        }
    }

}
