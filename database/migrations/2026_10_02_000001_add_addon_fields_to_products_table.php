<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extend products so SAP Business One addons can live next to physical goods.
     * Every column is nullable or defaulted so existing rows keep working.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('type', 20)->default('physical')->after('status')->index(); // physical | addon
            $table->string('integration', 30)->nullable()->after('type')->index();      // SePay | Shopify | Magento | Bank | EInvoice | VAS
            $table->string('billing', 20)->default('one_time')->after('integration');   // one_time | yearly | quote
            $table->string('summary', 300)->nullable()->after('description');
            $table->json('sap_versions')->nullable();
            $table->json('db_support')->nullable();  // ["sqlserver", "hana"]
            $table->json('features')->nullable();    // ["..."]
            $table->json('data_flow')->nullable();   // [{"from": "...", "to": "...", "note": "..."}]
            $table->json('faqs')->nullable();        // [{"q": "...", "a": "..."}]
            $table->json('gallery')->nullable();     // ["path", ...]
            $table->string('docs_url')->nullable();
            $table->string('demo_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['integration']);
            $table->dropColumn([
                'type', 'integration', 'billing', 'summary', 'sap_versions', 'db_support',
                'features', 'data_flow', 'faqs', 'gallery', 'docs_url', 'demo_url',
            ]);
        });
    }
};
