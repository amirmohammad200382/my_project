<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products_index = Permission::create(['name' => 'products_index']);
        $products_store = Permission::create(['name' => 'products_store']);
        $products_destroy = Permission::create(['name' => 'products_destroy']);
        $order_index = Permission::create(['name' => 'order_index']);
        $order_store = Permission::create(['name' => 'order_store']);
        $order_update = Permission::create(['name' => 'order_update']);
        $order_destroy = Permission::create(['name' => 'order_destroy']);

        $admin_role = Role::create(['name' => 'admin']);
        $admin_role->givePermissionTo([
            $order_destroy,
            $order_update,
            $order_store,
            $order_index,
            $products_destroy,
            $products_store,
            $products_index,
        ]);

        //customer

        $customer_role = Role::create(['name' => 'customer']);
        $customer_role->givePermissionTo([
            $order_destroy,
            $order_update,
            $order_store,
            $order_index
        ]);

        //seller

        $seller_role = Role::create(['name' => 'seller']);
        $seller_role->givePermissionTo([
            $products_destroy,
            $products_store,
            $products_index,
        ]);
    }
}
