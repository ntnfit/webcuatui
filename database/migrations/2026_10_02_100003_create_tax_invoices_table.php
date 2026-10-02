<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('direction', 10); // sold | purchase
            $table->string('source', 10)->default('standard'); // standard | mtt
            $table->string('mst_seller', 20);
            $table->string('mst_buyer', 20)->nullable();
            $table->string('seller_name')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('number', 30);
            $table->string('symbol', 30);
            $table->string('template', 30)->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->decimal('total_before_tax', 20, 2)->default(0);
            $table->decimal('total_tax', 20, 2)->default(0);
            $table->decimal('total_payment', 20, 2)->default(0);
            $table->string('currency', 10)->default('VND');
            $table->unsignedTinyInteger('status')->nullable(); // tthai
            $table->unsignedTinyInteger('check_status')->nullable(); // ttxly
            $table->json('raw');
            $table->json('detail')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'direction', 'symbol', 'number', 'mst_seller'], 'tax_invoices_identity_unique');
            $table->index(['company_id', 'issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_invoices');
    }
};
