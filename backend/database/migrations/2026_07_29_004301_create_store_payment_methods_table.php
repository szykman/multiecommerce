<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_payment_methods', function (Blueprint $table) {

            $table->id();

            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();

            // 'pix_manual' | 'mercadopago' | 'pagseguro' | 'stripe' (futuro)
            $table->string('provider');

            $table->boolean('enabled')->default(false);

            // JSON criptografado automaticamente pelo Laravel (cast
            // 'encrypted:array' no model) — cada provider guarda um
            // formato diferente aqui dentro:
            //   pix_manual:  {pix_key, pix_key_type, holder_name}
            //   mercadopago: {access_token, public_key}
            //   pagseguro:   {token, email}
            // Usar JSON flexível evita ter que criar coluna nova
            // toda vez que um gateway novo entrar (Stripe, etc.)
            $table->text('credentials')->nullable();

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->unique(['store_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_payment_methods');
    }
};
