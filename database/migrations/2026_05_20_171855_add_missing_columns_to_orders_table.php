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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->after('status');
            $table->decimal('discount_total', 10, 2)->default(0)->after('subtotal');
            $table->decimal('shipping_fee', 10, 2)->default(0)->after('discount_total');
            $table->json('shipping_address')->nullable()->after('total');
            $table->json('billing_address')->nullable()->after('shipping_address');
            $table->text('notes')->nullable()->after('billing_address');
            $table->date('estimated_delivery_date')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_total',
                'shipping_fee',
                'shipping_address',
                'billing_address',
                'notes',
                'estimated_delivery_date'
            ]);
        });
    }
};
