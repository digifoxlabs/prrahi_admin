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
        Schema::create('attendance_register_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_register_id');
            $table->string('employee_type', 20); // user | sales_person
            $table->unsignedBigInteger('employee_id');
            $table->string('identifier'); // user email OR sales login_id
            $table->string('display_name');
            $table->string('sort_name');
            $table->timestamps();

            $table->foreign('attendance_register_id', 'fk_att_participant_reg')
                ->references('id')
                ->on('attendance_registers')
                ->cascadeOnDelete();
            $table->unique(['attendance_register_id', 'employee_type', 'employee_id'], 'uniq_attendance_participant_employee');
            $table->index(['attendance_register_id', 'sort_name'], 'idx_att_part_reg_sort');
            $table->index('identifier', 'idx_att_part_identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_register_participants');
    }
};
