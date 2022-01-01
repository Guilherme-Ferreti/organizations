<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            'Gatos',
            'Cachorros',
            'Aves',
            'Soros',
            'Equipamentos altamente tecnológicos',
        ];

        foreach ($interests as $interest) {
            $rows[] = ['name' => $interest];
        }
        
        DB::table('interests')->insert($rows);
    }
}
