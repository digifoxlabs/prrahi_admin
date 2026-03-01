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
        Schema::table('attendance_entries', function (Blueprint $table) {
            $table->decimal('in_latitude', 10, 7)->nullable()->after('in_time');
            $table->decimal('in_longitude', 10, 7)->nullable()->after('in_latitude');
            $table->decimal('out_latitude', 10, 7)->nullable()->after('out_time');
            $table->decimal('out_longitude', 10, 7)->nullable()->after('out_latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_entries', function (Blueprint $table) {
            $table->dropColumn(['in_latitude', 'in_longitude', 'out_latitude', 'out_longitude']);
        });
    }
};