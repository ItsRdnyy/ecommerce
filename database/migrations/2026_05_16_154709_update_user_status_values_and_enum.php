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
        // Drop the status column temporarily
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Add it back with the new enum and default
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('role');
        });
        
        // Update existing users to approved (assuming all existing users were active)
        \DB::table('users')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the status column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        // Add it back with the old enum
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended'])->default('active')->after('role');
        });
        
        // Update existing users back to active
        \DB::table('users')->update(['status' => 'active']);
    }
};
