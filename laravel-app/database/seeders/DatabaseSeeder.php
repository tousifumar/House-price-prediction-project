<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            LocationSeeder::class,
            AmenitySeeder::class,
            MarketDataSeeder::class,
            MlModelSeeder::class,
            PropertySeeder::class,
        ]);
    }
}
