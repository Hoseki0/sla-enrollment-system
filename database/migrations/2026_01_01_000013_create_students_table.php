<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id', 20)->unique();
            $table->string('lrn', 12)->nullable()->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('suffix', 10)->nullable();
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('civil_status', ['Single', 'Married', 'Widowed'])->default('Single');
            $table->string('religion')->nullable();
            $table->string('nationality')->default('Filipino');
            $table->string('email')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->text('address');
            $table->string('barangay')->nullable();
            $table->string('municipality')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_contact', 20)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact', 20)->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_contact', 20)->nullable();
            $table->string('last_school_attended')->nullable();
            $table->string('last_school_address')->nullable();
            $table->enum('school_type', ['Public', 'Private'])->default('Public');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
