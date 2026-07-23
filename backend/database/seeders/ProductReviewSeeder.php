<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Product;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Carlos Silva',
            'Mariana Oliveira',
            'João Santos',
            'Ana Souza',
            'Pedro Almeida',
            'Fernanda Costa',
            'Lucas Ferreira',
            'Juliana Martins',
            'Rafael Gomes',
            'Camila Rocha',
        ];

        $comments = [
            'Produto excelente, gostei muito da qualidade.',
            'Chegou rápido e superou minhas expectativas.',
            'Muito bom, recomendo para todos.',
            'Ótimo custo benefício.',
            'Produto exatamente como descrito.',
            'Gostei bastante da compra.',
            'Qualidade acima do esperado.',
            'Minha experiência foi muito positiva.',
        ];

        $products = Product::all();

        foreach ($products as $product) {

            // evita duplicar se rodar novamente
            $exists = DB::table('product_reviews')
                ->where('product_id', $product->id)
                ->exists();

            if ($exists) {
                continue;
            }

            $total = rand(5, 30);

            for ($i = 0; $i < $total; $i++) {

                DB::table('product_reviews')->insert([

                    'store_id' => $product->store_id,

                    'product_id' => $product->id,

                    'name' => $names[array_rand($names)],

                    'email' => Str::lower(Str::random(8)) . '@example.com',

                    'rating' => rand(3,5),

                    'title' => [
                        'Excelente!',
                        'Muito bom',
                        'Recomendo',
                        'Gostei bastante',
                        'Compra aprovada'
                    ][array_rand([
                        'Excelente!',
                        'Muito bom',
                        'Recomendo',
                        'Gostei bastante',
                        'Compra aprovada'
                    ])],

                    'comment' => $comments[array_rand($comments)],

                    'approved' => true,

                    'created_at' => now()->subDays(rand(1,120)),

                    'updated_at' => now(),

                ]);
            }
        }
    }
}
