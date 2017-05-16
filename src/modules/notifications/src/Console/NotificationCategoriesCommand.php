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
 * @package    Notifications
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */


namespace Antares\Notifications\Console;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Antares\Notifications\Model\NotificationCategory;

class NotificationCategoriesCommand extends BaseCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'notifications:category-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of available notification categories';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $categories = NotificationCategory::all();
        $flatten    = [];
        foreach ($categories as $category) {
            $flatten[] = ['<info>' . $category->id . '</info>', '<fg=red>' . $category->name . '</fg=red>', '<info>' . $category->title . '</info>'];
        }

        if (count($flatten) > 0) {
            $this->table(['Id', 'Name', 'Title'], $flatten);
        } else {
            $this->error('No categories found');
        }
    }

}
