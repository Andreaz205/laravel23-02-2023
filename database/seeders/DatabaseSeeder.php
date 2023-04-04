<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
//        $this->call(BasicAdminPermissionSeeder::class);
//        $this->call(DiscountsAvailabilitySeeder::class);
//        $this->call(BonusesSeeder::class);
//        $this->call(InteriorSeeder::class);
        $this->call(MainPageSaleSeeder::class);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
