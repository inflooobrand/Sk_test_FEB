<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        for ($i=0; $i < 5; $i++) { 

            User::create([

                'name' => str_random(8),

                'email' => str_random(12).'@mail.com',

                'password' => bcrypt('123456')

            ]);

        }

    }
}
