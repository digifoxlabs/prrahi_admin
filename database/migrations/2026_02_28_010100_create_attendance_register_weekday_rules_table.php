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
        Schema::create('attendance_register_weekday_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_register_id');
            $table->unsignedTinyInteger('weekday'); // 0 => Sunday ... 6 => Saturday
            $table->time('default_in_time')->nullable();
            $table->time('default_out_time')->nullable();
            $table->timestamps();

            $table->foreign('attendance_register_id', 'fk_att_weekday_rule_reg')
                ->references('id')
                ->on('attendance_registers')
                ->cascadeOnDelete();
            $table->unique(['attendance_register_id', 'weekday'], 'uniq_attendance_weekday_rule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_register_weekday_rules');
    }
};
