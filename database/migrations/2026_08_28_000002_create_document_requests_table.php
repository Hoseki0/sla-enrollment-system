<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->enum('document_type', [
                'Form 137',
                'Good Moral Certificate',
                'Enrollment Certificate',
                'Diploma',
                'Transcript of Records',
                'Other',
            ]);
            $table->string('other_document')->nullable(); // if type = Other
            $table->text('purpose')->nullable();
            $table->integer('copies')->default(1);
            $table->enum('status', ['pending', 'processing', 'ready', 'released', 'cancelled'])->default('pending');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
