<?php

namespace Antares\Foundation\Notifications\Variables;

use Antares\Brands\Model\Brands;
use Antares\Model\User;
use Antares\Notifications\Contracts\ModelVariablesResoluble;
use Antares\Notifications\Services\ModuleVariables;
use Carbon\Carbon;
use Closure;
use Faker\Factory as Faker;

class CoreVariablesProvider implements ModelVariablesResoluble {

    /**
     * Applies the variables to the module container.
     *
     * @param ModuleVariables $moduleVariables
     */
    public function applyVariables(ModuleVariables $moduleVariables) : void {
        $moduleVariables
            ->modelDefinition('user', User::class, self::defaultUser())
            ->setAttributes([
                'id'        => 'ID',
                'email'     => 'Email',
                'firstname' => 'First Name',
                'lastname'  => 'Last Name',
                'fullname'  => 'Full Name',
                'status'    => 'Status',
            ]);

        $moduleVariables
            ->modelDefinition('brand', Brands::class, self::defaultBrand())
            ->setAttributes([
                'id'        => 'ID',
                'name'      => 'Name',
                'status'    => 'Status',
            ]);

        $moduleVariables->set('site.name', 'Site Name', function() {
            return memory('site.name');
        });

        $moduleVariables->set('time', 'Time', function() {
            return Carbon::now()->format('H:i');
        });

        $moduleVariables->set('date', 'Date', function() {
            return Carbon::now()->format('Y-m-d');
        });
    }

    /**
     * @return Closure
     */
    public static function defaultUser() : Closure {
        return function() {
            if($user = auth()->user()) {
                return $user;
            }

            $faker = Faker::create();

            return new User([
                'email'     => $faker->email,
                'firstname' => $faker->firstName,
                'lastname'  => $faker->lastName,
                'status'    => User::UNVERIFIED,
            ]);
        };
    }

    /**
     * @return Closure
     */
    public static function defaultBrand() : Closure {
        return function() {
            return Brands::query()->where('id', brand_id())->first();
        };
    }

}
