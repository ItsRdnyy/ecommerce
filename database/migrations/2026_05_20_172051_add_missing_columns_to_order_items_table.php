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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
            $table->string('variant_name')->nullable()->after('variant_id');
            $table->decimal('original_price', 10, 2)->default(0)->after('price');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('original_price');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'variant_id',
                'variant_name',
                'original_price',
                'discount_amount',
                'shipping_fee'
            ]);
        });
    }
};
