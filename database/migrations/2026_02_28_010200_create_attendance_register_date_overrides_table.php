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
        Schema::create('attendance_register_date_overrides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_register_id');
            $table->date('attendance_date');
            $table->boolean('is_holiday')->default(false);
            $table->string('holiday_name')->nullable();
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->timestamps();

            $table->foreign('attendance_register_id', 'fk_att_date_override_reg')
                ->references('id')
                ->on('attendance_registers')
                ->cascadeOnDelete();
            $table->unique(['attendance_register_id', 'attendance_date'], 'uniq_attendance_date_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_register_date_overrides');
    }
};
