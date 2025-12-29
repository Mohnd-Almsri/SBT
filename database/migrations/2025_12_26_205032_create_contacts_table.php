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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->index();

            $table->string('phone', 20)->nullable();
            $table->string('subject')->nullable();
            $table->text('message');

            $table->boolean('is_read')->default(false)->index();
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
            $table->index(['created_at']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
