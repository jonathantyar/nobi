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
        \App\Models\InvestmentProduct::factory(1)->create([
            'code' => 'ib',
            'name' => 'bitcoin'
        ]);
    }
}
