<?php
namespace Database\Seeders;
use App\Models\Interior;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InteriorSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        $titles = ['Гостиная', 'Спальня', 'Кухня', 'Прихожая', 'Детская'];
        foreach ($titles as $title) {
            Interior::create([
                'title' => $title,
            ]);
        }
    }
}
