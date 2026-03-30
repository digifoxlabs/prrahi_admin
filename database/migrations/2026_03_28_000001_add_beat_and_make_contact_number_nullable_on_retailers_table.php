<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retailers', function ($table) {
            $table->string('beat')->nullable()->after('address_line_1');
        });

        DB::statement('UPDATE retailers SET beat = address_line_2 WHERE beat IS NULL AND address_line_2 IS NOT NULL');
        DB::statement('ALTER TABLE retailers MODIFY contact_number VARCHAR(20) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE retailers MODIFY contact_number VARCHAR(20) NOT NULL');

        Schema::table('retailers', function ($table) {
            $table->dropColumn('beat');
        });
    }
};
