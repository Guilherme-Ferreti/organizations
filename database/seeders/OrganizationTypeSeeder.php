<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization_types = [
            'Clínica',
            'Laboratório',
            'Petshop',
        ];

        foreach ($organization_types as $organization_type) {
            $rows[] = ['name' => $organization_type];
        }
        
        DB::table('organization_types')->insert($rows);
    }
}
