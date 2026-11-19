<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('quarter');
            $table->unsignedTinyInteger('semester')->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->foreignId('encoded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['enrollment_id', 'subject_id', 'quarter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
    }
};
