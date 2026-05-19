<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First alter to include 'active' alongside 'approved' so we can migrate the data
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','active','rejected','suspended') DEFAULT 'pending'");

        // Then update any existing 'approved' users to 'active'
        DB::table('users')->where('status', 'approved')->update(['status' => 'active']);
        
        // Finally, alter the column to remove 'approved' from the ENUM list
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','active','rejected','suspended') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // To roll back, we add 'approved' back to the ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending','approved','rejected','active','suspended') DEFAULT 'pending'");
        
        // Note: we can't accurately know which 'active' users were originally 'approved', 
        // so we don't attempt to switch them back in the down method.
    }
};
