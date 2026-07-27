<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela própria, separada de password_reset_tokens (admin).
        // Necessário porque o e-mail do customer é único POR LOJA,
        // não globalmente — reaproveitar a tabela padrão (chaveada
        // só por e-mail) poderia colidir entre clientes de lojas
        // diferentes com o mesmo e-mail.
        Schema::create('customer_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_password_reset_tokens');
    }
};
