<?php

namespace Database\Seeders;

use App\Models\DiscountsAvailability;
use Illuminate\Database\Seeder;

class DiscountsAvailabilitySeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        $accessesTypes = ['accumulative', 'order', 'group', 'coupon'];
        foreach ($accessesTypes as $accessesType) {
            DiscountsAvailability::create([
               'type' => $accessesType,
                'is_available' => true
            ]);
        }
    }
}
