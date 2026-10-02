<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('customers')->cascadeOnDelete();
            $table->string('name');
            $table->string('mst', 20);
            $table->string('address')->nullable();
            $table->timestamps();

            $table->unique(['owner_id', 'mst']);
        });

        Schema::create('company_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('role', 10)->default('member');
            $table->timestamps();

            $table->unique(['company_id', 'customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_customer');
        Schema::dropIfExists('companies');
    }
};
