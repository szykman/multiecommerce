<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

$this->call([
    StoreSeeder::class,
    AdminUserSeeder::class,
    ProductReviewSeeder::class,
]);

//    User::factory()->create([
  //      'name' => 'Test User',
    //    'email' => 'test@example.com',
//    ]); 

   }
}
