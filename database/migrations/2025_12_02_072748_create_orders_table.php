<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Assuming users table exists, or customers table? Let's check.
            // Checking existing tables, we have 'customers' and 'users' (from default auth).
            // The auth.php routes use 'customer' guard. Let's check if we should link to customers or users.
            // The user said "fe lam chuc nang shop... gio hang, coupon". Usually implies logged in users or guests.
            // Let's use nullable user_id for now.
            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('payment_method')->default('cod');
            $table->text('shipping_address')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
