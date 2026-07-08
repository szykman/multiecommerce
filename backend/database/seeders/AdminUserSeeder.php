<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@multiecommerce.com.br',
            ],
            [
                'name' => 'Claudio',
                'password' => Hash::make('neo0103ECO@'),
                'store_id' => 1,
                'role' => 'admin',
                'active' => true,
            ]
        );
    }
}
