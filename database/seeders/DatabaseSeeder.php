<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ── Create Demo Tenant ────────────────────────────────────────
        $tenant = \App\Models\Tenant::firstOrCreate(
            ['email' => 'demo@store.com'],
            ['name' => 'Demo Store', 'business_type' => 'retail', 'phone' => '+1234567890', 'address' => '123 Main Street', 'subscription_plan' => 'pro']
        );

        // ── Create Main Branch ────────────────────────────────────────
        $branch = Branch::firstOrCreate(
            ['name' => 'Main Branch'],
            [
                'tenant_id' => $tenant->id,
                'address' => '123 Main Street',
                'phone'   => '+1234567890',
            ]
        );

        // ── Create Users ──────────────────────────────────────────────
        $users = [
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Admin User',
                'email'     => 'admin@store.com',
                'password'  => bcrypt('password'),
                'role'      => 'admin',
                'branch_id' => $branch->id,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Manager User',
                'email'     => 'manager@store.com',
                'password'  => bcrypt('password'),
                'role'      => 'manager',
                'branch_id' => $branch->id,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Cashier One',
                'email'     => 'cashier1@store.com',
                'password'  => bcrypt('password'),
                'role'      => 'cashier',
                'branch_id' => $branch->id,
                'is_active' => true,
            ],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Cashier Two',
                'email'     => 'cashier2@store.com',
                'password'  => bcrypt('password'),
                'role'      => 'cashier',
                'branch_id' => $branch->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
