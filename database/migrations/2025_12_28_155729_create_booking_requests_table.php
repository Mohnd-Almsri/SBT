<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_run_id')
                ->constrained('course_runs')
                ->cascadeOnDelete();

            $table->string('first_name');
            $table->string('last_name');

            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();

            $table->string('status', 30)->default('new'); // new|contacted|converted|rejected
            $table->text('note')->nullable();

            // مساحة توسع (UTM, IP, user_agent ...)
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['course_run_id', 'status']);
            $table->index(['email']);
            $table->index(['phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
