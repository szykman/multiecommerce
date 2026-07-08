<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Store;
use App\Models\StoreDomain;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
//        $store = Store::create([
  //          'name' => 'MultiEcommerce',
    //        'slug' => 'multiecommerce',
      //      'active' => true,
       // ]);

$store = Store::firstOrCreate(
    ['slug' => 'multiecommerce'],
    [
        'name' => 'MultiEcommerce',
        'active' => true,
    ]
);

//        StoreDomain::create([
  //          'store_id' => $store->id,
    //        'domain' => 'www.multiecommerce.com.br',
      //      'primary' => true,
       // ]);
StoreDomain::firstOrCreate(
    ['domain' => 'www.multiecommerce.com.br'],
    [
        'store_id' => $store->id,
        'primary' => true,
    ]
);

    }
}
