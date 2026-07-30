<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('provider');

            // Identificador da transação no gateway (txid do PIX
            // dinâmico, payment_id do Mercado Pago, etc.). Nulo no
            // PIX manual (não existe um txid de banco, é auto-declarado).
            $table->string('reference')->nullable();

            $table->decimal('amount', 10, 2);

            // pending | awaiting_confirmation | confirmed | cancelled
            $table->string('status')->default('pending');

            // Payload bruto devolvido pela API/webhook do gateway,
            // guardado para auditoria/depuração futura.
            $table->json('raw_response')->nullable();

            // Quem confirmou o pagamento: 'customer' (auto-declarou
            // "já paguei"), 'admin' (lojista conferiu manualmente),
            // 'webhook' (confirmação automática do gateway).
            $table->string('confirmed_by')->nullable();

            $table->timestamp('confirmed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
