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
        Schema::create('attendance_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_register_id')->constrained('attendance_registers')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('attendance_register_participants')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->time('in_time')->nullable();
            $table->time('out_time')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable(); // admin user id
            $table->string('source', 20)->default('admin'); // admin | api
            $table->timestamps();

            $table->unique(['attendance_register_id', 'participant_id', 'attendance_date'], 'uniq_attendance_entry_per_day');
            $table->index(['attendance_register_id', 'attendance_date']);
            $table->foreign('marked_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_entries');
    }
};