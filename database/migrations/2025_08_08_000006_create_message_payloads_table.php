<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('message_payloads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('message_id');
            $table->string('title', 255)->nullable();
            $table->text('payload')->nullable();
            $table->string('type', 100)->nullable();
            $table->string('image_url', 500)->nullable();
            $table->text('footer')->nullable();

            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('message_payloads');
    }
};
