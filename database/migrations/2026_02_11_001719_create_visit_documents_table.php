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
        Schema::create('visit_documents', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('visit_note_id');
            $table->string('file_path');
            $table->string('file_type')->nullable();

            $table->timestamps();

            $table->foreign('visit_note_id')
                  ->references('id')
                  ->on('visit_notes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_documents');
    }
};
