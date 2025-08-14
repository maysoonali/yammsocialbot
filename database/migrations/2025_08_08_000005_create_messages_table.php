<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id');
            $table->uuid('conversation_id');
            $table->uuid('sender_id')->nullable();
            $table->enum('sender_type', ['user', 'contact', 'bot'])->nullable();
            $table->string('message_type')->nullable();
            $table->text('content')->nullable();
            $table->string('content_type')->nullable();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->nullable();
            $table->boolean('private')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->boolean('payload_exist')->default(false);

            $table->foreign('account_id')->references('id')->on('accounts')->cascadeOnDelete();
            $table->foreign('conversation_id')->references('id')->on('conversations')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};