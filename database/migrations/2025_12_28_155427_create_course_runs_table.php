<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('course_runs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            // دفعة/تشغيلية
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();

            $table->unsignedInteger('capacity')->nullable();
            $table->decimal('price', 10, 2)->nullable();

            // حالة الدفعة (مفيد للتطوير)
            $table->string('status', 30)->default('open'); // open|closed|cancelled|draft
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['course_id', 'is_active']);
            $table->index(['is_active', 'starts_at']);
            $table->index(['status', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_runs');
    }
};
