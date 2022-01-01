<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(InterestSeeder::class);
        $this->call(OrganizationTypeSeeder::class);
        $this->call(UserSeeder::class);
    }
}
