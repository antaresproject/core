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
 * @package    Translations
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

 namespace Barryvdh\TranslationManager\Console;

use Illuminate\Console\Command;
use Barryvdh\TranslationManager\Manager;

class ResetCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'translations:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all translations from the database';

    /** @var \Barryvdh\TranslationManager\Manager  */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->manager->truncateTranslations();
        $this->info("All translations are deleted");
    }


}
