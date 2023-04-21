<?php

namespace Database\Seeders;

use App\Models\MainPageSale;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainPageSaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ([1, 2, 3, 4] as $item) {
            MainPageSale::create([
                'image_url' => url('/storage/images/no-image.jpg'),
                'image_path' => url('/images/no-image.jpg'),
                'value' => 0
            ]);
        }
    }
}

//        "staudenmeir/eloquent-eager-limit-x-laravel-adjacency-list": "1.0",
