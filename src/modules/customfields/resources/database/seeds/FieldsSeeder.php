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



use Illuminate\Database\Seeder;

class FieldsSeeder extends Seeder
{

    /**
     * Run the database seeding.
     *
     * @return void
     */
    public function run()
    {
        $this->down();

//        DB::table('tbl_fields_categories')->insert([
//            ['id' => 1, 'name' => 'users', 'description' => 'Custom Fields for users'],
//        ]);
//        DB::table('tbl_fields_groups')->insert([
//            [
//                'id'          => 1,
//                'category_id' => 1,
//                'name'        => 'user'
//            ],
//        ]);
        DB::table('tbl_fields_types')->insert([
            ['id' => 1, 'name' => 'input', 'type' => 'text', 'multi' => 0],
            ['id' => 2, 'name' => 'textarea', 'type' => NULL, 'multi' => 0],
            ['id' => 3, 'name' => 'input', 'type' => 'file', 'multi' => 0],
            ['id' => 4, 'name' => 'select', 'type' => NULL, 'multi' => 1],
            ['id' => 5, 'name' => 'input', 'type' => 'radio', 'multi' => 1],
            ['id' => 6, 'name' => 'input', 'type' => 'checkbox', 'multi' => 1],
        ]);

        DB::table('tbl_fields_validators')->insert([
            ['id' => 1, 'name' => 'required', 'description' => 'The field under validation must be present in the input data.', 'customizable' => 0, 'default' => NULL],
            ['id' => 2, 'name' => 'min', 'description' => 'The field under validation must have a minimum value. Strings, numerics, and files are evaluated in the same fashion as the size rule.', 'customizable' => 1, 'default' => 3],
            ['id' => 3, 'name' => 'max', 'description' => 'The field under validation must have a maximum value. Strings, numerics, and files are evaluated in the same fashion as the size rule.', 'customizable' => 1, 'default' => 255],
            ['id' => 4, 'name' => 'email', 'description' => 'The field under validation must be formatted as an e-mail address.', 'customizable' => 0, 'default' => NULL],
            ['id' => 5, 'name' => 'url', 'description' => 'The field under validation must be formatted as an URL.', 'customizable' => 0, 'default' => NULL],
            ['id' => 6, 'name' => 'numeric', 'description' => 'The field under validation must be formatted as an IP address.', 'customizable' => 0, 'default' => NULL],
            ['id' => 7, 'name' => 'string', 'description' => 'The field under validation must be a string type.', 'customizable' => 0, 'default' => NULL],
            ['id' => 9, 'name' => 'ip', 'description' => 'The field under validation must be formatted as an IP address.', 'customizable' => 0, 'default' => NULL],
            ['id' => 10, 'name' => 'date', 'description' => 'The field under validation must be a valid date according to the strtotime PHP function.', 'customizable' => 0, 'default' => NULL],
            ['id' => 11, 'name' => 'regex', 'description' => 'The field under validation must match the given regular expression.', 'customizable' => 1, 'default' => NULL],
            ['id' => 12, 'name' => 'min_checked', 'description' => 'Minmum number of checkboxes to select', 'customizable' => 1, 'default' => 1],
            ['id' => 13, 'name' => 'max_checked', 'description' => 'Maximum number of checkboxes to select', 'customizable' => 1, 'default' => 1],
            ['id' => 14, 'name' => 'custom', 'description' => 'Custom field', 'customizable' => 0, 'default' => NULL],
        ]);
        DB::table('tbl_fields_types_validators')->insert([
            ['type_id' => 1, 'validator_id' => 1],
            ['type_id' => 1, 'validator_id' => 2],
            ['type_id' => 1, 'validator_id' => 3],
            ['type_id' => 1, 'validator_id' => 4],
            ['type_id' => 1, 'validator_id' => 5],
            ['type_id' => 1, 'validator_id' => 6],
            ['type_id' => 1, 'validator_id' => 7],
            ['type_id' => 1, 'validator_id' => 10],
            ['type_id' => 1, 'validator_id' => 11],
            ['type_id' => 1, 'validator_id' => 9],
            ['type_id' => 2, 'validator_id' => 1],
            ['type_id' => 2, 'validator_id' => 2],
            ['type_id' => 2, 'validator_id' => 3],
            ['type_id' => 3, 'validator_id' => 1],
            ['type_id' => 4, 'validator_id' => 1],
            ['type_id' => 5, 'validator_id' => 1],
            ['type_id' => 6, 'validator_id' => 1],
            ['type_id' => 6, 'validator_id' => 12],
            ['type_id' => 6, 'validator_id' => 13],
            ['type_id' => 1, 'validator_id' => 14],
            ['type_id' => 2, 'validator_id' => 14],
            ['type_id' => 3, 'validator_id' => 14],
            ['type_id' => 4, 'validator_id' => 14],
            ['type_id' => 5, 'validator_id' => 14],
            ['type_id' => 6, 'validator_id' => 14],
        ]);
    }

    public function down()
    {
        DB::transaction(function() {
            DB::table('tbl_fields_types_validators')->delete();
            DB::table('tbl_fields_validators')->delete();
            DB::table('tbl_fields_types')->delete();
            DB::table('tbl_fields_groups')->delete();
            DB::table('tbl_fields_categories')->delete();
        });
    }

}
