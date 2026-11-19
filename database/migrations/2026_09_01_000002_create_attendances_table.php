<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Excused'])->default('Present');
            $table->string('time_in', 10)->nullable();
            $table->string('remarks')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
