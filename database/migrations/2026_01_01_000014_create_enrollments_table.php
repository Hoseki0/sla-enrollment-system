<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->string('enrollment_no', 30)->unique();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('encoded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->enum('level_type', ['JHS', 'SHS']);
            $table->string('grade_level');
            $table->string('track')->nullable();
            $table->string('strand')->nullable();
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer'])->nullable();
            $table->enum('enrollment_type', ['New', 'Old', 'Transferee', 'Returnee'])->default('New');
            $table->enum('status', ['Pending', 'Enrolled', 'Cancelled', 'Dropped'])->default('Pending');
            $table->date('enrollment_date')->useCurrent();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
