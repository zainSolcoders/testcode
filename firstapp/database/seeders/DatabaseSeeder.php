<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        \App\Models\Plan::create([
            'name' => 'Basic',
            'price' => '3.95',
            'products' => '3',
        ]);
        \App\Models\Plan::create([
            'name' => 'Standard',
            'price' => '5.95',
            'products' => '10',
        ]);
        \App\Models\Plan::create([
            'name' => 'Premium',
            'price' => '9.95',
            'products' => '40',
        ]);
        \App\Models\Plan::create([
            'name' => 'Ultimate',
            'price' => '19.95',
            'products' => '0',
        ]);
    }
}
