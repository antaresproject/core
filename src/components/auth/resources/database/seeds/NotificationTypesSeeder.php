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
                ['name' => 'mail', 'title' => 'Mail'],
                ['name' => 'sms', 'title' => 'Sms'],
                ['name' => 'notification', 'title' => 'Notification'],
                ['name' => 'alert', 'title' => 'Alert'],
        ];

        DB::table('tbl_notification_types')->insert($types);
    }

    /**
     * delete all database occurences for logger component
     */
    public function down()
    {
        if (Schema::hasTable('tbl_notification_types')) {
            DB::table('tbl_notification_types')->delete();
        }
    }

}
