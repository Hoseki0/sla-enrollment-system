<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('actor_type'); // 'admin' or 'student'
            $table->unsignedBigInteger('actor_id');
            $table->string('actor_name');
            $table->string('action');         // e.g. 'enrollment.status_changed'
            $table->string('subject_type')->nullable(); // e.g. 'Enrollment'
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('description');
            $table->json('meta')->nullable();  // extra data
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['actor_type', 'actor_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
