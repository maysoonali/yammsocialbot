<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('conversation_metrics', function (Blueprint $table) {
            $table->uuid('conversation_id')->primary();
            $table->interval('first_response_time')->nullable();
            $table->integer('total_messages')->nullable();
            $table->interval('average_response_time')->nullable();

            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('conversation_metrics');
    }
};
