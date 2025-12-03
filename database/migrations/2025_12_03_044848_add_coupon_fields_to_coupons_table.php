<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('quantity')->default(-1)->after('value'); // -1 = unlimited
            $table->decimal('max_discount', 15, 2)->default(0)->after('quantity'); // 0 = no limit for percentage
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'max_discount']);
        });
    }
};
