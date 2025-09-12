<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type', 100);
            $table->jsonb('raw_payload');
            $table->timestamp('received_at')->nullable();

            // Optional relation to messages
            $table->uuid('message_id')->nullable();
            $table
                ->foreign('message_id')
                ->references('id')
                ->on('messages')
                ->nullOnDelete(); // set to NULL if message deleted
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhook_events');
    }
};
