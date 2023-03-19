<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BasicAdminPermissionSeeder extends Seeder
{
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        $permissions = [
            'user list',
            'user create',
            'user edit',
            'user delete',
            'contact list',
            'contact create',
            'contact edit',
            'contact delete',
            'admin list',
            'admin create',
            'admin edit',
            'admin delete',
            'review list',
            'review create',
            'review edit',
            'review delete',
            'room list',
            'room create',
            'room edit',
            'room delete',
            'sale list',
            'sale create',
            'sale edit',
            'sale delete',
            'category list',
            'category create',
            'category edit',
            'category delete',
            'product list',
            'product create',
            'product edit',
            'product delete',
            'order list',
            'order create',
            'order edit',
            'order delete',
            'discount list',
            'discount create',
            'discount edit',
            'discount delete',
            'price list',
            'price create',
            'price edit',
            'price delete',
            'material list',
            'material create',
            'material edit',
            'material delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        // create roles and assign existing permissions
//        $role1 = Role::create(['name' => 'writer']);
//        $role1->givePermissionTo('permission list');
//        $role1->givePermissionTo('role list');
//        $role1->givePermissionTo('user list');
//        $role1->givePermissionTo('post list');
//        $role1->givePermissionTo('post create');
//        $role1->givePermissionTo('post edit');
//        $role1->givePermissionTo('post delete');
//        $role2 = Role::create(['name' => 'admin']);
//        foreach ($permissions as $permission) {
//            $role2->givePermissionTo($permission);
//        }
        $role3 = Role::create(['name' => 'super-admin']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider
        // create demo users
        $user = \App\Models\Admin::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@mail.ru',
        ]);
//        foreach ($permissions as $permission) {
//            $user->givePermissionTo($permission);
//        }
        $user->assignRole($role3);
//        $user->givePermissionTo('');
//        $user = \App\Models\User::factory()->create([
//            'name' => 'Admin User',
//            'email' => 'admin@laraveltuts.com',
//        ]);
//        $user->givePermissionTo('post list');
//        $user->assignRole($role2);
        $user = \App\Models\Admin::factory()->create([
            'name' => 'Example User',
            'email' => 'user@mail.ru',
        ]);
//        $user->assignRole($role1);
    }
}
