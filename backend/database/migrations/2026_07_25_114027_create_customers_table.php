<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('store_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            // Documento (CPF/CNPJ) — não obrigatório neste sprint,
            // mas já deixamos o campo pronto pois será necessário
            // para emissão de cobrança PIX no checkout.
            $table->string('document')->nullable();

            $table->timestamp('email_verified_at')->nullable();

            $table->string('password');

            $table->boolean('active')->default(true);

            $table->rememberToken();

            $table->timestamps();

            // E-mail único DENTRO da mesma loja — não globalmente.
            // O mesmo e-mail pode existir em lojas (tenants) diferentes,
            // cada um como um cliente independente.
            $table->unique(['store_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
