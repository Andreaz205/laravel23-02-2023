<?php

namespace Database\Seeders;

use App\Models\Bonus;
use Illuminate\Database\Seeder;

class BonusesSeeder extends Seeder
{
    public function run()
    {
        Bonus::create();
    }
}
